// Variables globales
let currentContactId = null
let activeTab = "all"
let selectedFiles = []
let selectedImages = []
let searchTimeout = null
let firebaseDatabase = null
let currentUserId = null
const messageListeners = new Map() // Para manejar múltiples listeners
const processedMessageIds = new Set() // Para evitar mensajes duplicados
const firebase = window.firebase // Declare the firebase variable
const lucide = window.lucide // Declare the lucide variable
const contactsData = new Map() // Para almacenar datos de contactos

// Elementos DOM (sin cambios)
const searchInput = document.getElementById("search-input")
const contactsContainer = document.getElementById("contacts-container")
const contactsLoading = document.getElementById("contacts-loading")
const tabButtons = document.querySelectorAll(".tab-button")
const emptyState = document.getElementById("empty-state")
const chatInterface = document.getElementById("chat-interface")
const messagesContainer = document.getElementById("messages-container")
const messageForm = document.getElementById("message-form")
const messageInput = document.getElementById("message-input")
const contactIdInput = document.getElementById("contact-id")
const chatAvatar = document.getElementById("chat-avatar")
const chatName = document.getElementById("chat-name")
const chatStatus = document.getElementById("chat-status")
const newChatModal = document.getElementById("new-chat-modal")
const newChatForm = document.getElementById("new-chat-form")
const fileInput = document.getElementById("file-input")
const imageInput = document.getElementById("image-input")
const filePreviewArea = document.getElementById("file-preview-area")
const filePreviewList = document.getElementById("file-preview-list")
const workerSearch = document.getElementById("worker-search")
const workerResults = document.getElementById("worker-results")
const selectedWorkerId = document.getElementById("selected-worker-id")
const selectedWorker = document.getElementById("selected-worker")
const unreadBadge = document.getElementById("unread-badge")
const newChatMessageInput = newChatForm.querySelector('textarea[name="message"]') // Nuevo: Referencia al input de mensaje del modal

// Datos iniciales y rutas desde Blade (sin cambios)
const initialContacts = window.appData.initialContacts
const initialStats = window.appData.initialStats
const allWorkers = window.appData.allWorkers
const routes = window.appData.routes
const csrfToken = window.appData.routes.csrfToken
const firebaseConfig = window.appData.firebaseConfig

// Función para actualizar último mensaje del contacto (ya no se usa directamente para DOM)
function updateContactLastMessage(contactId, lastMessage, time) {
  // Esta función ya no manipula el DOM directamente,
  // sino que se basa en contactsData y displayContacts()
  // para mantener la consistencia.
}

// Inicializar Firebase (sin cambios)
function initializeFirebase() {
  try {
    const config = {
      databaseURL: firebaseConfig.databaseURL,
      projectId: firebaseConfig.projectId,
    }

    firebase.initializeApp(config)
    firebaseDatabase = firebase.database()

    console.log("Firebase inicializado correctamente")

    currentUserId = getCurrentUserId()

    if (currentUserId) {
      loadContactsFromFirebase()
      setupGlobalRealtimeListeners()
    }
  } catch (error) {
    console.error("Error inicializando Firebase:", error)
  }
}

// Función para obtener el ID del usuario actual (sin cambios)
function getCurrentUserId() {
  const userIdMeta = document.querySelector('meta[name="user-id"]')
  if (userIdMeta) {
    return Number.parseInt(userIdMeta.getAttribute("content"))
  }
  if (window.currentUserId) {
    return Number.parseInt(window.currentUserId)
  }
  console.warn("No se pudo obtener el ID del usuario actual")
  return null
}

// Cargar contactos desde Firebase
function loadContactsFromFirebase() {
  if (!currentUserId || !firebaseDatabase) return

  console.log("Cargando contactos desde Firebase para usuario:", currentUserId)

  const userConversationsRef = firebaseDatabase.ref(`user_conversations/${currentUserId}`)

  userConversationsRef.on("value", (snapshot) => {
    const conversations = snapshot.val()
    console.log("Conversaciones obtenidas de Firebase:", conversations)

    contactsData.clear() // Limpiar datos existentes antes de recargar

    if (conversations) {
      const contactPromises = Object.keys(conversations).map((conversationId) => {
        const conversation = conversations[conversationId]
        const otherUserId = conversation.other_user_id

        return loadContactData(otherUserId, conversationId)
      })

      Promise.all(contactPromises).then(() => {
        displayContacts()
        contactsLoading.style.display = "none"
      })
    } else {
      contactsLoading.innerHTML = '<div class="text-gray-500 text-center py-4">No hay conversaciones</div>'
      displayContacts() // Asegurarse de que la lista se actualice incluso si está vacía
    }
  })
}

// Cargar datos de un contacto específico
function loadContactData(contactId, conversationId) {
  return new Promise((resolve) => {
    // Buscar información del trabajador en allWorkers
    const workerInfo = allWorkers.find((w) => w.id === contactId)

    if (!workerInfo) {
      console.warn("No se encontró información del trabajador:", contactId)
      resolve()
      return
    }

    // Obtener último mensaje y contador de no leídos
    const conversationRef = firebaseDatabase.ref(`conversations/${conversationId}`)
    const unreadRef = firebaseDatabase.ref(`user_conversations/${currentUserId}/${conversationId}/unread_count`)

    Promise.all([conversationRef.child("last_message").once("value"), unreadRef.once("value")])
      .then(([lastMessageSnapshot, unreadSnapshot]) => {
        const lastMessage = lastMessageSnapshot.val()
        const unreadCount = unreadSnapshot.val() || 0

        const contactData = {
          id: contactId,
          name: workerInfo.name,
          avatar: workerInfo.avatar,
          online: workerInfo.online,
          role: workerInfo.role,
          lastMessage: lastMessage ? lastMessage.contenido : "Sin mensajes",
          time: lastMessage ? formatTimestamp(lastMessage.timestamp) : "",
          unreadCount: unreadCount,
          important: false,
          group: false,
          updatedAt: lastMessage ? lastMessage.timestamp : 0,
        }

        contactsData.set(contactId, contactData)
        resolve()
      })
      .catch((error) => {
        console.error("Error cargando datos del contacto:", error)
        resolve()
      })
  })
}

// Mostrar contactos en la interfaz
function displayContacts() {
  const sortedContacts = Array.from(contactsData.values()).sort((a, b) => b.updatedAt - a.updatedAt)

  contactsContainer.innerHTML = ""

  if (sortedContacts.length === 0) {
    contactsContainer.innerHTML = '<div class="text-gray-500 text-center py-4">No hay conversaciones</div>'
    return
  }

  sortedContacts.forEach((contact) => {
    const contactElement = createContactElement(contact)
    contactsContainer.appendChild(contactElement)
  })

  // Actualizar contador de no leídos
  updateUnreadBadge()

  // Seleccionar primer contacto si no hay ninguno seleccionado y hay contactos
  if (!currentContactId && sortedContacts.length > 0) {
    selectContact(sortedContacts[0].id)
  }

  // Re-aplicar el filtro de la pestaña activa
  filterContacts(activeTab)
}

// Crear elemento de contacto
function createContactElement(contact) {
  const contactElement = document.createElement("div")
  contactElement.className =
    "contact-item p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors hover-scale"
  contactElement.dataset.contactId = contact.id
  contactElement.dataset.unread = contact.unreadCount > 0 ? "true" : "false"
  contactElement.dataset.important = contact.important ? "true" : "false"

  contactElement.innerHTML = `
    <div class="flex items-center space-x-3">
      <div class="relative">
        <img src="${contact.avatar}" alt="${contact.name}" class="w-10 h-10 rounded-full">
        ${contact.online ? '<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>' : ""}
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
          <p class="contact-name text-sm font-medium text-gray-900 truncate">${contact.name}</p>
          <p class="contact-time text-xs text-gray-500">${contact.time}</p>
        </div>
        <p class="contact-last-message text-sm text-gray-500 truncate">${contact.lastMessage}</p>
      </div>
      ${
        contact.unreadCount > 0
          ? `
        <div class="bg-blue-500 text-white text-xs font-medium px-2 py-1 rounded-full min-w-[20px] text-center">
          ${contact.unreadCount}
        </div>
      `
          : ""
      }
    </div>
  `

  contactElement.addEventListener("click", function () {
    const contactId = Number.parseInt(this.dataset.contactId)
    selectContact(contactId)
  })

  return contactElement
}

// Actualizar badge de no leídos
function updateUnreadBadge() {
  const totalUnread = Array.from(contactsData.values()).reduce((total, contact) => total + contact.unreadCount, 0)
  unreadBadge.textContent = totalUnread
}

// Configurar listeners globales
function setupGlobalRealtimeListeners() {
  if (!currentUserId || !firebaseDatabase) return

  console.log("Configurando listeners globales para usuario:", currentUserId)

  const messagesRef = firebaseDatabase.ref("messages")

  messagesRef.on("child_added", (snapshot) => {
    const messageId = snapshot.key
    const messageData = snapshot.val()

    // Procesar mensajes donde el usuario actual es remitente O destinatario
    if (
      (messageData.remitente_id === currentUserId || messageData.destinatario_id === currentUserId) &&
      !processedMessageIds.has(messageId)
    ) {
      processedMessageIds.add(messageId) // Marcar mensaje como procesado
      handleNewRealtimeMessage(messageData)
    }
  })

  // Listener para actualizaciones de user_conversations (para contadores de no leídos, etc.)
  const userConversationsRef = firebaseDatabase.ref(`user_conversations/${currentUserId}`)

  userConversationsRef.on("child_changed", (snapshot) => {
    const conversationId = snapshot.key
    const conversationData = snapshot.val()

    console.log("Conversación actualizada:", conversationId, conversationData)
    // Recargar datos del contacto para actualizar la lista y el badge de no leídos
    loadContactData(conversationData.other_user_id, conversationId).then(() => {
      displayContacts()
    })
  })
}

// Manejar nuevo mensaje en tiempo real
function handleNewRealtimeMessage(messageData) {
  console.log("Procesando mensaje en tiempo real:", messageData)

  const isSentByCurrentUser = messageData.remitente_id === currentUserId
  const contactId = isSentByCurrentUser ? messageData.destinatario_id : messageData.remitente_id

  let contact = contactsData.get(contactId)

  if (!contact) {
    // Si el contacto no existe en el mapa (nueva conversación), cargar sus datos
    const workerInfo = allWorkers.find((w) => w.id === contactId)
    if (workerInfo) {
      contact = {
        id: contactId,
        name: workerInfo.name,
        avatar: workerInfo.avatar,
        online: workerInfo.online,
        role: workerInfo.role,
        lastMessage: messageData.contenido,
        time: formatTimestamp(messageData.timestamp),
        unreadCount: 0, // Se incrementará a continuación si es necesario
        important: false,
        group: false,
        updatedAt: messageData.timestamp,
      }
      contactsData.set(contactId, contact)
    } else {
      console.warn("No se encontró información del trabajador para el participante del mensaje:", contactId)
      return // No se puede procesar el mensaje sin información del contacto
    }
  }

  // Incrementar contador de no leídos solo si es un mensaje recibido y no estamos en el chat activo
  if (!isSentByCurrentUser && currentContactId !== contactId) {
    contact.unreadCount += 1
  }

  // Actualizar último mensaje y timestamp
  contact.lastMessage = messageData.contenido
  contact.time = formatTimestamp(messageData.timestamp)
  contact.updatedAt = messageData.timestamp
  contactsData.set(contactId, contact) // Asegurarse de que el mapa se actualice

  displayContacts() // Volver a renderizar la lista de contactos para reflejar los cambios

  // Mostrar mensaje en el chat activo
  if (currentContactId === contactId) {
    addRealtimeMessageToChat(messageData, isSentByCurrentUser)

    // Marcar como leído solo si es un mensaje recibido y la ventana está enfocada
    if (!isSentByCurrentUser && document.hasFocus()) {
      markAsReadInFirebase(contactId)
    }
  }

  // Mostrar notificación si la ventana no está activa, el mensaje es recibido Y NO ESTÁ LEÍDO
  if (!isSentByCurrentUser && !document.hasFocus() && messageData.leido === false) {
    showNotification(messageData)
    playNotificationSound()
  }
}

// Cargar mensajes desde Firebase
function loadMessagesFromFirebase(contactId) {
  if (!currentUserId || !firebaseDatabase) return

  messagesContainer.innerHTML =
    '<div class="flex justify-center py-4"><div class="text-gray-500">Cargando mensajes...</div></div>'

  const messagesRef = firebaseDatabase.ref("messages")

  // Consultar mensajes de la conversación
  messagesRef.orderByChild("timestamp").once("value", (snapshot) => {
    const allMessages = snapshot.val()
    const conversationMessages = []

    if (allMessages) {
      Object.keys(allMessages).forEach((messageId) => {
        const message = allMessages[messageId]
        if (
          (message.remitente_id === currentUserId && message.destinatario_id === contactId) ||
          (message.remitente_id === contactId && message.destinatario_id === currentUserId)
        ) {
          // Asegurarse de que el objeto attachment tenga los campos necesarios
          if (message.attachment && message.attachment.url && message.attachment.type) {
            message.attachment.name =
              message.attachment.name || message.contenido.replace(/^(📎|🖼️)\s*/, "") || "archivo"
            message.attachment.size = message.attachment.size || "Desconocido" // Firebase no guarda el tamaño, se puede estimar o dejar así
          }
          conversationMessages.push({
            id: messageId,
            ...message,
          })
        }
      })
    }

    // Ordenar mensajes por timestamp
    conversationMessages.sort((a, b) => a.timestamp - b.timestamp)

    displayMessagesFromFirebase(conversationMessages)
  })
}

// Mostrar mensajes cargados desde Firebase
function displayMessagesFromFirebase(messages) {
  messagesContainer.innerHTML = ""

  if (messages.length === 0) {
    messagesContainer.innerHTML =
      '<div class="flex justify-center py-4"><div class="text-gray-500">No hay mensajes</div></div>'
    return
  }

  messages.forEach((message) => {
    const messageEl = document.createElement("div")
    const isSent = message.remitente_id === currentUserId
    messageEl.className = `flex ${isSent ? "justify-end" : "justify-start"} fade-in`

    const time = formatTimestamp(message.timestamp)
    const readStatus = isSent ? (message.leido ? "✓✓" : "✓") : "" // Mostrar ✓✓ si es enviado y leído, ✓ si solo enviado

    let html = ""
    const attachmentHtml = message.attachment
      ? `
        <div class="mt-2 p-2 ${isSent ? "bg-blue-600 rounded border border-blue-400" : "bg-gray-50 rounded border border-gray-200"}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="${getFileIcon(message.attachment.type || "application/octet-stream")}" class="w-4 h-4"></i>
                    <div>
                        <p class="text-xs font-medium">${message.attachment.name}</p>
                        <p class="text-xs ${isSent ? "opacity-75" : "text-gray-500"}">${message.attachment.size || "Desconocido"}</p>
                    </div>
                </div>
                <a href="${message.attachment.url}" target="_blank" class="text-xs ${isSent ? "bg-blue-700 hover:bg-blue-800 text-white" : "bg-gray-200 hover:bg-gray-300 text-gray-700"} px-2 py-1 rounded transition-colors">
                    Descargar
                </a>
            </div>
        </div>
    `
      : ""

    if (isSent) {
      html = `
        <div class="max-w-xs lg:max-w-md">
          <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
            ${message.contenido ? `<p class="text-sm">${message.contenido}</p>` : ""}
            ${attachmentHtml}
          </div>
          <div class="flex items-center justify-end mt-1 space-x-1">
            <span class="text-xs text-gray-500">${time}</span>
            <span class="text-xs text-gray-500">${readStatus}</span>
          </div>
        </div>
      `
    } else {
      html = `
        <div class="max-w-xs lg:max-w-md">
          <div class="bg-white text-gray-900 rounded-lg px-4 py-2 border border-gray-200">
            ${message.contenido ? `<p class="text-sm">${message.contenido}</p>` : ""}
            ${attachmentHtml}
          </div>
          <p class="text-xs text-gray-500 mt-1">${time}</p>
        </div>
      `
    }

    messageEl.innerHTML = html
    messagesContainer.appendChild(messageEl)
  })

  lucide.createIcons()
  messagesContainer.scrollTop = messagesContainer.scrollHeight
}

// Agregar mensaje en tiempo real al chat
function addRealtimeMessageToChat(messageData, isSent) {
  const messageEl = document.createElement("div")
  messageEl.className = `flex ${isSent ? "justify-end" : "justify-start"} fade-in realtime-message`

  const time = formatTimestamp(messageData.timestamp)
  const readStatus = isSent ? (messageData.leido ? "✓✓" : "✓") : "" // Mostrar ✓✓ si es enviado y leído, ✓ si solo enviado

  let html = ""
  const attachmentHtml = messageData.attachment
    ? `
        <div class="mt-2 p-2 ${isSent ? "bg-blue-600 rounded border border-blue-400" : "bg-gray-50 rounded border border-gray-200"}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i data-lucide="${getFileIcon(messageData.attachment.type || "application/octet-stream")}" class="w-4 h-4"></i>
                    <div>
                        <p class="text-xs font-medium">${messageData.attachment.name}</p>
                        <p class="text-xs ${isSent ? "opacity-75" : "text-gray-500"}">${messageData.attachment.size || "Desconocido"}</p>
                    </div>
                </div>
                <a href="${messageData.attachment.url}" target="_blank" class="text-xs ${isSent ? "bg-blue-700 hover:bg-blue-800 text-white" : "bg-gray-200 hover:bg-gray-300 text-gray-700"} px-2 py-1 rounded transition-colors">
                    Descargar
                </a>
            </div>
        </div>
    `
    : ""

  if (isSent) {
    html = `
      <div class="max-w-xs lg:max-w-md">
        <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
          ${messageData.contenido ? `<p class="text-sm">${messageData.contenido}</p>` : ""}
          ${attachmentHtml}
        </div>
        <div class="flex items-center justify-end mt-1 space-x-1">
          <span class="text-xs text-gray-500">${time}</span>
          <span class="text-xs text-gray-500">${readStatus}</span>
        </div>
      </div>
    `
  } else {
    html = `
      <div class="max-w-xs lg:max-w-md">
        <div class="bg-white text-gray-900 rounded-lg px-4 py-2 border border-gray-200">
          ${messageData.contenido ? `<p class="text-sm">${messageData.contenido}</p>` : ""}
          ${attachmentHtml}
        </div>
        <p class="text-xs text-gray-500 mt-1">${time}</p>
      </div>
    `
  }

  messageEl.innerHTML = html
  messagesContainer.appendChild(messageEl)

  // Scroll al final con animación suave
  messagesContainer.scrollTo({
    top: messagesContainer.scrollHeight,
    behavior: "smooth",
  })

  // Reinicializar iconos
  lucide.createIcons()
}

// Formatear timestamp
function formatTimestamp(timestamp) {
  const date = new Date(timestamp * 1000)
  const now = new Date()

  if (date.toDateString() === now.toDateString()) {
    return date.toLocaleTimeString("es-ES", {
      hour: "2-digit",
      minute: "2-digit",
    })
  } else if (date.toDateString() === new Date(now.getTime() - 86400000).toDateString()) {
    return "Ayer"
  } else {
    return date.toLocaleDateString("es-ES", {
      day: "2-digit",
      month: "2-digit",
    })
  }
}

// Marcar como leído en Firebase
function markAsReadInFirebase(contactId) {
  if (!currentUserId || !firebaseDatabase) return

  const conversationId = currentUserId < contactId ? `${currentUserId}_${contactId}` : `${contactId}_${currentUserId}`

  // Resetear contador de no leídos
  firebaseDatabase.ref(`user_conversations/${currentUserId}/${conversationId}/unread_count`).set(0)

  // Actualizar en datos locales
  const contact = contactsData.get(contactId)
  if (contact) {
    contact.unreadCount = 0
    contactsData.set(contactId, contact)
    displayContacts() // Re-render para actualizar el badge y la lista
  }

  // Marcar mensajes individuales como leídos en Firebase
  // Esto es importante para el estado de los checks (✓✓)
  firebaseDatabase
    .ref("messages")
    .orderByChild("destinatario_id")
    .equalTo(currentUserId)
    .once("value", (snapshot) => {
      snapshot.forEach((childSnapshot) => {
        const message = childSnapshot.val()
        if (message.remitente_id === contactId && message.leido === false) {
          firebaseDatabase.ref(`messages/${childSnapshot.key}`).update({ leido: true })
        }
      })
    })
}

function showNotification(messageData) {
  if ("Notification" in window && Notification.permission === "granted") {
    const contact = contactsData.get(messageData.remitente_id)
    const contactName = contact ? contact.name : "Usuario"

    new Notification(`Nuevo mensaje de ${contactName}`, {
      body: messageData.contenido,
      icon: "/favicon.ico",
    })
  }
}

// Reproducir sonido de notificación
function playNotificationSound() {
  try {
    const audio = new Audio("/sounds/notification.mp3")
    audio.volume = 0.3
    audio.play().catch((e) => console.log("No se pudo reproducir el sonido:", e))
  } catch (e) {
    console.log("Error reproduciendo sonido:", e)
  }
}

// Función para enviar mensaje con Firebase (solo texto)
function sendMessageWithFirebase(messageText, contactId) {
  if (!currentUserId || !firebaseDatabase) {
    console.error("Firebase no está inicializado")
    return Promise.reject(new Error("Firebase no inicializado"))
  }

  try {
    const timestamp = Math.floor(Date.now() / 1000)
    const messageId = firebaseDatabase.ref("messages").push().key

    const messageData = {
      id: messageId,
      remitente_id: currentUserId,
      destinatario_id: contactId,
      contenido: messageText,
      timestamp: timestamp,
      fecha: new Date().toISOString().split("T")[0],
      hora: new Date().toTimeString().split(" ")[0],
      leido: false, // Siempre false al enviar un nuevo mensaje
    }

    const conversationId = currentUserId < contactId ? `${currentUserId}_${contactId}` : `${contactId}_${currentUserId}`

    const updates = {}
    updates[`messages/${messageId}`] = messageData
    updates[`conversations/${conversationId}/last_message`] = messageData
    updates[`conversations/${conversationId}/updated_at`] = timestamp
    updates[`user_conversations/${currentUserId}/${conversationId}`] = {
      other_user_id: contactId,
      updated_at: timestamp,
    }

    // Para el destinatario, incrementar contador de no leídos
    firebaseDatabase
      .ref(`user_conversations/${contactId}/${conversationId}/unread_count`)
      .transaction((currentCount) => {
        return (currentCount || 0) + 1
      })

    updates[`user_conversations/${contactId}/${conversationId}`] = {
      other_user_id: currentUserId,
      updated_at: timestamp,
    }

    return firebaseDatabase.ref().update(updates)
  } catch (error) {
    console.error("Error enviando mensaje a Firebase:", error)
    return Promise.reject(error)
  }
}

// Función para seleccionar contacto
function selectContact(contactId) {
  currentContactId = contactId
  contactIdInput.value = contactId

  const contactItems = document.querySelectorAll(".contact-item")
  let selectedContact = null

  contactItems.forEach((item) => {
    const itemId = Number.parseInt(item.dataset.contactId)
    if (itemId === contactId) {
      item.classList.add("bg-blue-50")
      selectedContact = contactsData.get(contactId)

      // Limpiar badge de no leídos en el DOM y en los datos
      const badge = item.querySelector(".bg-blue-500")
      if (badge) {
        badge.remove()
      }
      item.dataset.unread = "false"
      if (selectedContact) {
        selectedContact.unreadCount = 0
        contactsData.set(contactId, selectedContact)
      }
    } else {
      item.classList.remove("bg-blue-50")
    }
  })

  if (!selectedContact) {
    console.error("No se pudo encontrar el contacto con ID:", contactId)
    return
  }

  emptyState.classList.add("hidden")
  chatInterface.classList.remove("hidden")

  chatName.textContent = selectedContact.name
  chatStatus.textContent = selectedContact.online ? "En línea" : "Desconectado"
  chatStatus.className = selectedContact.online ? "text-sm text-green-500" : "text-sm text-gray-500"
  chatAvatar.src = selectedContact.avatar

  // Cargar mensajes desde Firebase
  loadMessagesFromFirebase(contactId)
  clearFiles()

  // Marcar como leído en Firebase
  markAsReadInFirebase(contactId)
}

// Función mejorada para enviar mensaje con respuesta inmediata
function sendMessage(e) {
  e.preventDefault()

  const message = messageInput.value.trim()
  if (!message && selectedFiles.length === 0 && selectedImages.length === 0) return
  if (!currentContactId) return

  // Enviar mensaje de texto a Firebase
  if (message) {
    sendMessageWithFirebase(message, currentContactId)
  }

  // Enviar archivos/imágenes al servidor Laravel
  if (selectedFiles.length > 0 || selectedImages.length > 0) {
    const formData = new FormData()
    formData.append("contact_id", currentContactId)
    if (message) formData.append("message", message) // Incluir mensaje de texto si existe

    selectedFiles.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })

    selectedImages.forEach((image, index) => {
      formData.append(`images[${index}]`, image)
    })

    fetch(routes.sendMessage, {
      method: "POST",
      headers: {
        "X-CSRF-TOKEN": csrfToken,
      },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          console.log("Archivos enviados correctamente al servidor y a Firebase.")
          // Los mensajes de archivo/imagen serán manejados por el listener de Firebase
          // y aparecerán en el chat cuando Firebase los envíe.
        } else {
          console.error("Error enviando archivos:", data.error)
        }
      })
      .catch((error) => {
        console.error("Error:", error)
      })
  }

  // Limpiar input y archivos
  messageInput.value = ""
  clearFiles()
}

// Solicitar permisos de notificación
function requestNotificationPermission() {
  if ("Notification" in window && Notification.permission === "default") {
    Notification.requestPermission().then((permission) => {
      console.log("Permiso de notificación:", permission)
    })
  }
}

// Funciones existentes (mantener todas las funciones originales)
function searchWorkers(query) {
  clearTimeout(searchTimeout)

  workerResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Cargando trabajadores...</div>'
  workerResults.classList.remove("hidden")

  searchTimeout = setTimeout(
    () => {
      console.log("Iniciando búsqueda de trabajadores:", { query: query || "(vacío)" })

      fetch(routes.searchWorkers, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrfToken,
          Accept: "application/json",
        },
        body: JSON.stringify({ query: query || "" }),
      })
        .then((response) => {
          console.log("Respuesta recibida:", response.status, response.statusText)
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`)
          }
          return response.json()
        })
        .then((data) => {
          console.log("Datos recibidos:", data)
          if (data.success) {
            displayWorkerResults(data.workers)
          } else {
            console.error("Error en búsqueda:", data.error)
            workerResults.innerHTML = `<div class="p-3 text-sm text-red-500">Error: ${data.error}</div>`
            workerResults.classList.remove("hidden")
          }
        })
        .catch((error) => {
          console.error("Error completo:", error)
          workerResults.innerHTML = `<div class="p-3 text-sm text-red-500">Error de conexión: ${error.message}</div>`
          workerResults.classList.remove("hidden")
        })
    },
    query && query.length > 0 ? 300 : 100,
  )
}

function displayWorkerResults(workers) {
  workerResults.innerHTML = ""

  if (workers.length === 0) {
    workerResults.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron trabajadores</div>'
  } else {
    workers.forEach((worker) => {
      const workerItem = document.createElement("div")
      workerItem.className = "p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
      workerItem.innerHTML = `
        <div class="flex items-center space-x-3">
          <img src="${worker.avatar}" alt="${worker.name}" class="w-8 h-8 rounded-full">
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">${worker.name}</p>
            <p class="text-xs text-gray-500">${worker.role}</p>
          </div>
          ${worker.online ? '<div class="w-2 h-2 bg-green-500 rounded-full"></div>' : ""}
        </div>
      `
      workerItem.addEventListener("click", () => selectWorker(worker))
      workerResults.appendChild(workerItem)
    })
  }

  workerResults.classList.remove("hidden")
}

function selectWorker(worker) {
  selectedWorkerId.value = worker.id
  workerSearch.value = worker.name
  workerResults.classList.add("hidden")

  document.getElementById("selected-worker-avatar").src = worker.avatar
  document.getElementById("selected-worker-name").textContent = worker.name
  document.getElementById("selected-worker-role").textContent = worker.role
  selectedWorker.classList.remove("hidden")

  lucide.createIcons()
}

function clearSelectedWorker() {
  selectedWorkerId.value = ""
  workerSearch.value = ""
  selectedWorker.classList.add("hidden")
  workerResults.classList.add("hidden")
}

function handleFileSelect(event, type) {
  const files = Array.from(event.target.files)

  if (type === "files") {
    files.forEach((file) => {
      if (!selectedFiles.find((f) => f.name === file.name && f.size === file.size)) {
        selectedFiles.push(file)
      }
    })
  } else if (type === "images") {
    files.forEach((file) => {
      if (!selectedImages.find((f) => f.name === file.name && f.size === file.size)) {
        selectedImages.push(file)
      }
    })
  }

  updateFilePreview()
}

function updateFilePreview() {
  const allFiles = [...selectedFiles, ...selectedImages]

  if (allFiles.length === 0) {
    filePreviewArea.classList.add("hidden")
    return
  }

  filePreviewArea.classList.remove("hidden")
  filePreviewList.innerHTML = ""

  allFiles.forEach((file, index) => {
    const fileItem = document.createElement("div")
    fileItem.className = "flex items-center justify-between bg-white p-2 rounded border text-xs"

    const fileSize = formatFileSize(file.size)
    const fileIcon = getFileIcon(file.type)
    const isImage = file.type.startsWith("image/")

    fileItem.innerHTML = `
      <div class="flex items-center space-x-2 flex-1 min-w-0">
        <i data-lucide="${fileIcon}" class="w-3 h-3 text-gray-500 flex-shrink-0"></i>
        <span class="truncate">${file.name}</span>
        <span class="text-gray-400">(${fileSize})</span>
        <span class="text-blue-500">${isImage ? "Imagen" : "Archivo"}</span>
      </div>
      <button type="button" onclick="removeFile(${index}, '${isImage ? "images" : "files"}')" class="text-red-500 hover:text-red-700 ml-2">
        <i data-lucide="x" class="w-3 h-3"></i>
      </button>
    `

    filePreviewList.appendChild(fileItem)
  })

  lucide.createIcons()
}

function removeFile(index, type) {
  if (type === "images") {
    selectedImages.splice(index - selectedFiles.length, 1)
  } else {
    selectedFiles.splice(index, 1)
  }
  updateFilePreview()
}

function clearFiles() {
  selectedFiles = []
  selectedImages = []
  fileInput.value = ""
  imageInput.value = ""
  updateFilePreview()
}

function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes"
  const k = 1024
  const sizes = ["Bytes", "KB", "MB", "GB"]
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Number.parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
}

function getFileIcon(mimeType) {
  if (mimeType.startsWith("image/")) return "image"
  if (mimeType.startsWith("video/")) return "video"
  if (mimeType.startsWith("audio/")) return "music"
  if (mimeType.includes("pdf")) return "file-text"
  if (mimeType.includes("word") || mimeType.includes("document")) return "file-text"
  if (mimeType.includes("excel") || mimeType.includes("spreadsheet")) return "file-spreadsheet"
  if (mimeType.includes("powerpoint") || mimeType.includes("presentation")) return "presentation"
  return "file"
}

function filterContacts(tab) {
  activeTab = tab

  tabButtons.forEach((button) => {
    if (button.dataset.tab === tab) {
      button.classList.add("border-b-2", "border-blue-500", "text-blue-600")
      button.classList.remove("text-gray-500")
    } else {
      button.classList.remove("border-b-2", "border-blue-500", "text-blue-600")
      button.classList.add("text-gray-500")
    }
  })

  const contactItems = document.querySelectorAll(".contact-item")
  contactItems.forEach((item) => {
    if (tab === "all") {
      item.classList.remove("hidden")
    } else if (tab === "unread") {
      if (item.dataset.unread === "true") {
        item.classList.remove("hidden")
      } else {
        item.classList.add("hidden")
      }
    }
  })
}

function searchContacts(query) {
  query = query.toLowerCase()

  const contactItems = document.querySelectorAll(".contact-item")
  contactItems.forEach((item) => {
    const name = item.querySelector(".contact-name").textContent.toLowerCase()
    const message = item.querySelector(".contact-last-message").textContent.toLowerCase()

    if (name.includes(query) || message.includes(query)) {
      item.classList.remove("hidden")
    } else {
      item.classList.add("hidden")
    }
  })
}

function openNewChatModal() {
  newChatModal.classList.remove("hidden")
  newChatModal.classList.add("flex")
  displayWorkerResults(allWorkers)
  newChatMessageInput.value = "" // Limpiar el campo de mensaje al abrir el modal
}

function closeNewChatModal() {
  newChatModal.classList.add("hidden")
  newChatModal.classList.remove("flex")
  clearSelectedWorker()
  newChatMessageInput.value = "" // Limpiar el campo de mensaje al cerrar el modal
}

// Event Listeners
document.addEventListener("DOMContentLoaded", () => {
  // Inicializar Firebase
  initializeFirebase()

  // Solicitar permisos de notificación
  requestNotificationPermission()

  // Inicializar iconos
  lucide.createIcons()

  // Cambio de pestañas
  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      filterContacts(this.dataset.tab)
    })
  })

  // Búsqueda de contactos en la lista principal
  searchInput.addEventListener("input", function () {
    searchContacts(this.value)
  })

  // Búsqueda de trabajadores en modal
  workerSearch.addEventListener("input", function () {
    searchWorkers(this.value)
  })

  workerSearch.addEventListener("focus", function () {
    searchWorkers(this.value)
  })

  workerSearch.addEventListener("click", function () {
    searchWorkers(this.value)
  })

  // Ocultar resultados al hacer clic fuera
  document.addEventListener("click", (e) => {
    if (!workerSearch.contains(e.target) && !workerResults.contains(e.target)) {
      workerResults.classList.add("hidden")
    }
  })

  // Selección de archivos
  fileInput.addEventListener("change", (e) => {
    handleFileSelect(e, "files")
  })

  // Selección de imágenes
  imageInput.addEventListener("change", (e) => {
    handleFileSelect(e, "images")
  })

  // Envío de mensaje
  messageForm.addEventListener("submit", sendMessage)

  // Envío de mensaje con Enter
  messageInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault()
      sendMessage(e)
    }
  })

  // Nuevo chat
  newChatForm.addEventListener("submit", function (e) {
    e.preventDefault()

    if (!selectedWorkerId.value) {
      alert("Por favor selecciona un trabajador")
      return
    }

    const messageText = newChatMessageInput.value.trim()
    if (!messageText) {
      alert("Por favor escribe un mensaje")
      return
    }

    // Mostrar indicador de carga
    const submitButton = this.querySelector('button[type="submit"]')
    const originalText = submitButton.textContent
    submitButton.textContent = "Enviando..."
    submitButton.disabled = true

    // Enviar a Firebase inmediatamente
    const contactId = Number.parseInt(selectedWorkerId.value)
    sendMessageWithFirebase(messageText, contactId)
      .then(() => {
        console.log("Mensaje enviado a Firebase")

        // Cerrar modal inmediatamente
        closeNewChatModal()

        // Agregar el nuevo contacto a la lista si no existe
        addNewContactToList(contactId, messageText)

        // Seleccionar el nuevo contacto
        setTimeout(() => {
          selectContact(contactId)
        }, 100)

        // Enviar a servidor Laravel en segundo plano (para registro en DB si es necesario, o para archivos)
        const formData = new FormData(this)
        fetch(routes.newChat, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": csrfToken,
          },
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              console.log("Chat guardado en servidor")
            } else {
              console.error("Error guardando chat en servidor:", data.error)
            }
          })
          .catch((error) => {
            console.error("Error:", error)
          })
          .finally(() => {
            submitButton.textContent = originalText
            submitButton.disabled = false
          })
      })
      .catch((error) => {
        console.error("Error enviando a Firebase:", error)
        alert("Error al iniciar el chat")
        submitButton.textContent = originalText
        submitButton.disabled = false
      })
  })

  // Cerrar modal al hacer clic fuera
  newChatModal.addEventListener("click", function (e) {
    if (e.target === this) {
      closeNewChatModal()
    }
  })

  // Marcar como leído cuando la ventana recibe foco
  window.addEventListener("focus", () => {
    if (currentContactId) {
      markAsReadInFirebase(currentContactId)
    }
  })
})

// Limpiar listeners al cerrar la página
window.addEventListener("beforeunload", () => {
  messageListeners.forEach((ref) => {
    ref.off()
  })
  messageListeners.clear()
})

// Nueva función para agregar contacto a la lista
function addNewContactToList(contactId, lastMessage) {
  // Buscar si el contacto ya existe
  const existingContact = contactsData.get(contactId)
  if (existingContact) {
    // Si existe, solo actualizar
    existingContact.lastMessage = lastMessage
    existingContact.time = formatTimestamp(Math.floor(Date.now() / 1000))
    existingContact.updatedAt = Math.floor(Date.now() / 1000)
    contactsData.set(contactId, existingContact)
    displayContacts()
    return
  }

  // Buscar información del trabajador en allWorkers
  const workerInfo = allWorkers.find((w) => w.id === contactId)
  if (!workerInfo) {
    console.error("No se encontró información del trabajador:", contactId)
    return
  }

  // Crear nuevo contacto
  const newContact = {
    id: contactId,
    name: workerInfo.name,
    avatar: workerInfo.avatar,
    online: workerInfo.online,
    role: workerInfo.role,
    lastMessage: lastMessage,
    time: formatTimestamp(Math.floor(Date.now() / 1000)),
    unreadCount: 0,
    important: false,
    group: false,
    updatedAt: Math.floor(Date.now() / 1000),
  }

  contactsData.set(contactId, newContact)
  displayContacts()
}
