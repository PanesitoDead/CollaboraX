// Variables globales
let currentContactId = null
let activeTab = "all"
let selectedFiles = []
let selectedImages = []
let searchTimeout = null

// Elementos DOM
const searchInput = document.getElementById("search-input")
const contactsContainer = document.getElementById("contacts-container")
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

// Datos iniciales y rutas desde Blade
const initialContacts = window.appData.initialContacts
const initialStats = window.appData.initialStats
const allWorkers = window.appData.allWorkers
const routes = window.appData.routes
const csrfToken = window.appData.routes.csrfToken

// Funciones de búsqueda de trabajadores
function searchWorkers(query) {
  clearTimeout(searchTimeout)

  // Mostrar indicador de carga
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
  ) // Reducir delay para búsquedas vacías
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

  // Mostrar trabajador seleccionado
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

// Funciones de archivos
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

// Seleccionar contacto
function selectContact(contactId) {
  // Actualizar ID actual
  currentContactId = contactId
  contactIdInput.value = contactId

  // Obtener datos del contacto
  const contactItems = document.querySelectorAll(".contact-item")
  let selectedContact = null

  contactItems.forEach((item) => {
    const itemId = Number.parseInt(item.dataset.contactId)
    if (itemId === contactId) {
      // Marcar como seleccionado
      item.classList.add("bg-blue-50")

      // Obtener datos del contacto
      const nameElement = item.querySelector(".contact-name")
      const onlineIndicator = item.querySelector(".bg-green-500")
      const avatarElement = item.querySelector("img")

      if (nameElement && avatarElement) {
        selectedContact = {
          id: itemId,
          name: nameElement.textContent.trim(),
          online: onlineIndicator !== null,
          avatar: avatarElement.src,
        }
      }

      // Eliminar badge de no leídos
      const badge = item.querySelector(".bg-blue-500")
      if (badge) {
        badge.remove()
      }
    } else {
      // Desmarcar otros
      item.classList.remove("bg-blue-50")
    }
  })

  if (!selectedContact) {
    console.error("No se pudo encontrar el contacto con ID:", contactId)
    return
  }

  // Actualizar interfaz de chat
  emptyState.classList.add("hidden")
  chatInterface.classList.remove("hidden")

  // Actualizar header del chat
  chatName.textContent = selectedContact.name
  chatStatus.textContent = selectedContact.online ? "En línea" : "Desconectado"
  chatStatus.className = selectedContact.online ? "text-sm text-green-500" : "text-sm text-gray-500"
  chatAvatar.src = selectedContact.avatar

  // Cargar mensajes desde el servidor
  loadMessages(contactId)

  // Asegurarse de que el área de previsualización esté oculta al cambiar de contacto
  clearFiles()
}

// Cargar mensajes desde el servidor
function loadMessages(contactId) {
  // Mostrar loading
  messagesContainer.innerHTML =
    '<div class="flex justify-center py-4"><div class="text-gray-500">Cargando mensajes...</div></div>'

  fetch(`${routes.getMessages}${contactId}`, {
    method: "GET",
    headers: {
      "X-CSRF-TOKEN": csrfToken,
    },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayMessages(data.messages)
      } else {
        messagesContainer.innerHTML =
          '<div class="flex justify-center py-4"><div class="text-red-500">Error al cargar mensajes</div></div>'
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      messagesContainer.innerHTML =
        '<div class="flex justify-center py-4"><div class="text-red-500">Error al cargar mensajes</div></div>'
    })
}

// Función mejorada para mostrar mensajes en la interfaz
function displayMessages(messages) {
  messagesContainer.innerHTML = ""

  messages.forEach((message) => {
    const messageEl = document.createElement("div")
    messageEl.className = `flex ${message.sent ? "justify-end" : "justify-start"} fade-in`

    let html = ""

    if (message.sent) {
      html = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
                        ${message.text ? `<p class="text-sm">${message.text}</p>` : ""}
                        ${
                          message.attachment
                            ? `
                            <div class="mt-2 p-2 bg-blue-600 rounded border border-blue-400">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="${getFileIcon(message.attachment.type || "application/octet-stream")}" class="w-4 h-4"></i>
                                        <div>
                                            <p class="text-xs font-medium">${message.attachment.name}</p>
                                            <p class="text-xs opacity-75">${message.attachment.size}</p>
                                        </div>
                                    </div>
                                    <a href="${message.attachment.url}" target="_blank" class="text-xs bg-blue-700 hover:bg-blue-800 px-2 py-1 rounded transition-colors">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        `
                            : ""
                        }
                    </div>
                    <div class="flex items-center justify-end mt-1 space-x-1">
                        <span class="text-xs text-gray-500">${message.time}</span>
                        <span class="text-xs text-gray-500">${message.read ? "✓✓" : "✓"}</span>
                    </div>
                </div>
            `
    } else {
      html = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-white text-gray-900 rounded-lg px-4 py-2 border border-gray-200">
                        ${message.text ? `<p class="text-sm">${message.text}</p>` : ""}
                        ${
                          message.attachment
                            ? `
                            <div class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <i data-lucide="${getFileIcon(message.attachment.type || "application/octet-stream")}" class="w-4 h-4"></i>
                                        <div>
                                            <p class="text-xs font-medium">${message.attachment.name}</p>
                                            <p class="text-xs text-gray-500">${message.attachment.size}</p>
                                        </div>
                                    </div>
                                    <a href="${message.attachment.url}" target="_blank" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded text-gray-700 transition-colors">
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        `
                            : ""
                        }
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${message.time}</p>
                </div>
            `
    }

    messageEl.innerHTML = html
    messagesContainer.appendChild(messageEl)
  })

  // Reinicializar iconos
  lucide.createIcons()

  // Scroll al final
  messagesContainer.scrollTop = messagesContainer.scrollHeight
}

// Función para actualizar el último mensaje en tiempo real (estilo WhatsApp)
function updateContactLastMessage(contactId, lastMessage, time) {
  const contactItem = document.querySelector(`[data-contact-id="${contactId}"]`)
  if (contactItem) {
    // Actualizar el último mensaje
    const lastMessageElement = contactItem.querySelector(".contact-last-message")
    if (lastMessageElement) {
      lastMessageElement.textContent = lastMessage
    }

    // Actualizar la hora
    const timeElement = contactItem.querySelector(".contact-time")
    if (timeElement) {
      timeElement.textContent = time
    }

    // Mover el contacto al principio de la lista (comportamiento tipo WhatsApp)
    const contactsContainer = document.getElementById("contacts-container")
    if (contactsContainer && contactItem !== contactsContainer.firstElementChild) {
      // Agregar efecto de transición suave
      contactItem.style.transition = "all 0.3s ease"

      // Mover al principio
      contactsContainer.insertBefore(contactItem, contactsContainer.firstElementChild)

      // Si este contacto está seleccionado, mantener el fondo azul
      if (Number.parseInt(contactItem.dataset.contactId) === currentContactId) {
        contactItem.classList.add("bg-blue-50")
      }
    }
  }
}

// Enviar mensaje con actualización en tiempo real
function sendMessage(e) {
  e.preventDefault()

  const message = messageInput.value.trim()
  if (!message && selectedFiles.length === 0 && selectedImages.length === 0) return
  if (!currentContactId) return

  // Crear FormData para enviar archivos
  const formData = new FormData()
  formData.append("contact_id", currentContactId)
  if (message) formData.append("message", message)

  selectedFiles.forEach((file, index) => {
    formData.append(`files[${index}]`, file)
  })

  selectedImages.forEach((image, index) => {
    formData.append(`images[${index}]`, image)
  })

  // Mostrar mensaje inmediatamente en el chat (optimistic UI)
  if (message || selectedFiles.length > 0 || selectedImages.length > 0) {
    const currentTime = new Date().toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" })
    let previewMessage = message

    // Si hay archivos, mostrar preview
    if (selectedFiles.length > 0) {
      previewMessage = previewMessage ? previewMessage : "📎 Archivo"
    }
    if (selectedImages.length > 0) {
      previewMessage = previewMessage ? previewMessage : "🖼️ Imagen"
    }

    // Actualizar inmediatamente la lista de contactos
    updateContactLastMessage(currentContactId, previewMessage, currentTime)

    // Mostrar mensaje temporal en el chat
    showTemporaryMessage(message, selectedFiles, selectedImages)
  }

  // Limpiar input y archivos inmediatamente
  messageInput.value = ""
  clearFiles()

  // Enviar a servidor
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
        // Recargar mensajes para mostrar la versión final desde el servidor
        loadMessages(currentContactId)
      } else {
        console.error("Error al enviar el mensaje")
        alert("Error al enviar el mensaje")
        // Recargar para quitar el mensaje temporal
        loadMessages(currentContactId)
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      alert("Error al enviar el mensaje")
      // Recargar para quitar el mensaje temporal
      loadMessages(currentContactId)
    })
}

// Función para mostrar mensaje temporal inmediatamente
function showTemporaryMessage(text, files, images) {
  const messageEl = document.createElement("div")
  messageEl.className = "flex justify-end fade-in temporary-message"

  const currentTime = new Date().toLocaleTimeString("es-ES", { hour: "2-digit", minute: "2-digit" })

  let attachmentHtml = ""
  const allFiles = [...files, ...images]
  if (allFiles.length > 0) {
    attachmentHtml = allFiles
      .map(
        (file) => `
            <div class="mt-2 p-2 bg-blue-600 rounded border border-blue-400">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i data-lucide="${getFileIcon(file.type)}" class="w-4 h-4"></i>
                        <div>
                            <p class="text-xs font-medium">${file.name}</p>
                            <p class="text-xs opacity-75">${formatFileSize(file.size)}</p>
                        </div>
                    </div>
                    <span class="text-xs bg-blue-700 px-2 py-1 rounded">
                        Enviando...
                    </span>
                </div>
            </div>
        `,
      )
      .join("")
  }

  messageEl.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="bg-blue-500 text-white rounded-lg px-4 py-2">
                ${text ? `<p class="text-sm">${text}</p>` : ""}
                ${attachmentHtml}
            </div>
            <div class="flex items-center justify-end mt-1 space-x-1">
                <span class="text-xs text-gray-500">${currentTime}</span>
                <span class="text-xs text-gray-500">⏳</span>
            </div>
        </div>
    `

  messagesContainer.appendChild(messageEl)
  messagesContainer.scrollTop = messagesContainer.scrollHeight

  // Reinicializar iconos
  lucide.createIcons()
}

// Filtrar contactos por pestaña
function filterContacts(tab) {
  activeTab = tab

  // Actualizar UI de pestañas
  tabButtons.forEach((button) => {
    if (button.dataset.tab === tab) {
      button.classList.add("border-b-2", "border-blue-500", "text-blue-600")
      button.classList.remove("text-gray-500")
    } else {
      button.classList.remove("border-b-2", "border-blue-500", "text-blue-600")
      button.classList.add("text-gray-500")
    }
  })

  // Filtrar contactos
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

// Buscar contactos (filtrado en el cliente)
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

// Abrir modal de nuevo chat
function openNewChatModal() {
  newChatModal.classList.remove("hidden")
  newChatModal.classList.add("flex")

  // Cargar automáticamente todos los trabajadores al abrir el modal
  // Usamos los datos ya cargados en la vista para evitar otra petición AJAX inicial
  displayWorkerResults(allWorkers)
}

// Cerrar modal de nuevo chat
function closeNewChatModal() {
  newChatModal.classList.add("hidden")
  newChatModal.classList.remove("flex")
  clearSelectedWorker()
}

// Event Listeners
document.addEventListener("DOMContentLoaded", () => {
  // Inicializar iconos
  lucide.createIcons()

  // Seleccionar primer contacto si existe
  const firstContact = document.querySelector(".contact-item")
  if (firstContact) {
    const contactId = Number.parseInt(firstContact.dataset.contactId)
    selectContact(contactId)
  }

  // Click en contactos
  document.querySelectorAll(".contact-item").forEach((item) => {
    item.addEventListener("click", function () {
      const contactId = Number.parseInt(this.dataset.contactId)
      selectContact(contactId)
    })
  })

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

  // Mostrar todos los trabajadores al hacer clic en el input del modal
  workerSearch.addEventListener("focus", function () {
    searchWorkers(this.value) // Siempre buscar, incluso si está vacío
  })

  // También agregar evento click para asegurar que se muestren los trabajadores
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
          closeNewChatModal()
          // Recargar la página para mostrar el nuevo chat
          window.location.reload()
        } else {
          alert("Error al iniciar el chat: " + (data.error || "Desconocido"))
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        alert("Error al iniciar el chat")
      })
  })

  // Cerrar modal al hacer clic fuera
  newChatModal.addEventListener("click", function (e) {
    if (e.target === this) {
      closeNewChatModal()
    }
  })
})
