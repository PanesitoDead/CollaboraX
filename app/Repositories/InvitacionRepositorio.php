<?php

 namespace App\Repositories;

use App\Models\Invitacion;
use App\Models\MiembroEquipo;
use Illuminate\Database\Eloquent\Builder;

class InvitacionRepositorio extends RepositorioBase
{
    protected MiembroEquipo $miembroEquipoModel;
    public function __construct(Invitacion $model, MiembroEquipo $miembroEquipoModel)
    {
        parent::__construct($model);
        $this->miembroEquipoModel = $miembroEquipoModel;
    }

    public function getInvitacionesPorEquipo($equipo)
    {
        return $this->model->where('equipo_id', $equipo)->get();
    }

    public function aceptarInvitacion(int $id): bool
    {
        $invitacion = $this->getById($id);
        if (!$invitacion) {
            return false;
        }
        
        $invitacion->estado = 'ACEPTADA';
        $invitacion->fecha_respuesta = now();
        $invitacion->save();

        // Aquí podrías agregar lógica adicional para manejar la aceptación de la invitación, como asignar el usuario al equipo.
        $miembroEquipo = new MiembroEquipo();
        $miembroEquipo->trabajador_id = $invitacion->trabajador_id;
        $miembroEquipo->equipo_id = $invitacion->equipo_id;
        $miembroEquipo->fecha_union = now();
        $miembroEquipo->activo = true; // Asumimos que el miembro está activo al aceptar la invitación
        $miembroEquipo->save();

        return true;
    }

    public function cancelarInivitacion(int $id): bool
    {
        $invitacion = $this->getById($id);
        if (!$invitacion) {
            return false;
        }
        
        $invitacion->estado = 'CANCELADA';
        $invitacion->save();

        return $this->delete($id);
    }


    protected function aplicarRango(Builder $consulta, ?array $range): void
    {
        if ($range['field'] && $range['values']) {

            if ($range['values']['start'] === $range['values']['end']) {
                $consulta->where($range['field'], $range['values']['start']);
            } else {
            $consulta->whereBetween($range['field'], [$range['values']['start'], $range['values']['end']]);
            }

        }
    }

    protected function aplicarFiltros(Builder $consulta, array $filtros): void
    {
        // Quitamos todos los valores nulos o cadenas vacías
        $filtros = array_filter(
            $filtros,
            fn($value) => !is_null($value) && $value !== ''
        );
        foreach ($filtros as $key => $value) {
            switch ($key) {
                case 'id':
                    $consulta->where('empresas.id', $value);
                    break;
                case 'plan_servicio_id':
                    $consulta->where('empresas.plan_servicio_id', $value);
                    break;
                case 'estado':
                    $this->aplicarJoinCondicional($consulta, 'usuarios', 'usuario_id', '=', 'usuarios.id');
                    $consulta->where('usuarios.activo', $value);
                    break;
                default:
                    $consulta->where($key, $value);
                    break;
            }
        }
    }

    protected function aplicarBusqueda(Builder $consulta, ?string $searchTerm, ?string $searchColumn): void
    {
        // Si no hay columna de búsqueda, ponemos la columna por defecto
        if (!$searchColumn) {
            $searchColumn = 'nombre'; // Columna por defecto para búsqueda
        }
        if ($searchTerm && $searchColumn) {
            switch ($searchColumn) {
                case 'id':
                    $consulta->where('empresas.id', 'like', $searchTerm);
                    break;
                case 'nombre':
                    $consulta->where('empresas.nombre', 'like', '%' . $searchTerm . '%');
                    break;
                default:
                    $consulta->where($searchColumn, 'like', '%' . $searchTerm . '%');
                    break;
            }
            
        }
    }

    protected function aplicarOrdenamiento(Builder $consulta, ?string $sortField, ?string $sortOrder): void
    {
        if ($sortField && $sortOrder) {
            switch ($sortField) {
                case 'id':
                    $consulta->orderBy('empresa.id', $sortOrder);
                    break;
                case 'nombre':
                    $consulta->orderBy('empresa.nombre', $sortOrder);
                    break;
                default:
                    $consulta->orderBy($sortField, $sortOrder);
                    break;
            }
        }
    }

}
 