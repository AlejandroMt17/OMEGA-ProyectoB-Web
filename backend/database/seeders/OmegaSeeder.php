<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * OmegaSeeder — Datos de prueba para el Sistema de Control de Asistencias
 *
 * Crea:
 *  - 2 Docentes
 *  - 2 Instituciones (una por docente)
 *  - 2 Rubros por institución (Ordinario 80%, Extraordinario 60%)
 *  - 3 Grupos por institución (6 en total)
 *  - 10 Alumnos
 *  - Cada alumno inscrito en 2-3 grupos
 *  - 5 Sesiones cerradas por grupo con asistencias variadas
 *  - 1 Suscripción por docente (plan básico)
 *
 * Contraseña para todos: Omega2026
 */
class OmegaSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('asistencias')->truncate();
        DB::table('sesiones')->truncate();
        DB::table('grupo_alumnos')->truncate();
        DB::table('grupos')->truncate();
        DB::table('rubros_evaluacion')->truncate();
        DB::table('instituciones')->truncate();
        DB::table('suscripciones')->truncate();
        DB::table('pagos')->truncate();
        DB::table('personal_access_tokens')->truncate();
        // Conservar usuarios existentes y agregar los nuevos
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $password = Hash::make('Omega2026');
        $now = Carbon::now();

        // ─── DOCENTES ────────────────────────────────────────────────────────
        $docente1Id = DB::table('usuarios')->insertGetId([
            'nombre'     => 'Carlos',
            'ap_pat'     => 'Mendoza',
            'ap_mat'     => 'Rios',
            'email'      => 'cmendoza@omega.com',
            'contrasenia'=> $password,
            'rol'        => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $docente2Id = DB::table('usuarios')->insertGetId([
            'nombre'     => 'Laura',
            'ap_pat'     => 'Gutierrez',
            'ap_mat'     => 'Vega',
            'email'      => 'lgutierrez@omega.com',
            'contrasenia'=> $password,
            'rol'        => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ─── ALUMNOS ─────────────────────────────────────────────────────────
        $alumnos = [
            ['nombre'=>'Sofia',    'ap_pat'=>'Ramirez',   'ap_mat'=>'Luna',     'email'=>'sramirez@omega.com'],
            ['nombre'=>'Diego',    'ap_pat'=>'Torres',    'ap_mat'=>'Mora',     'email'=>'dtorres@omega.com'],
            ['nombre'=>'Valeria',  'ap_pat'=>'Castro',    'ap_mat'=>'Perez',    'email'=>'vcastro@omega.com'],
            ['nombre'=>'Andres',   'ap_pat'=>'Flores',    'ap_mat'=>'Reyes',    'email'=>'aflores@omega.com'],
            ['nombre'=>'Camila',   'ap_pat'=>'Herrera',   'ap_mat'=>'Diaz',     'email'=>'cherrera@omega.com'],
            ['nombre'=>'Luis',     'ap_pat'=>'Vargas',    'ap_mat'=>'Ortiz',    'email'=>'lvargas@omega.com'],
            ['nombre'=>'Isabella', 'ap_pat'=>'Morales',   'ap_mat'=>'Jimenez',  'email'=>'imorales@omega.com'],
            ['nombre'=>'Sebastian','ap_pat'=>'Ruiz',      'ap_mat'=>'Mendez',   'email'=>'sruiz@omega.com'],
            ['nombre'=>'Fernanda', 'ap_pat'=>'Salinas',   'ap_mat'=>'Cruz',     'email'=>'fsalinas@omega.com'],
            ['nombre'=>'Miguel',   'ap_pat'=>'Romero',    'ap_mat'=>'Aguilar',  'email'=>'mromero@omega.com'],
        ];

        $alumnoIds = [];
        foreach ($alumnos as $alumno) {
            $alumnoIds[] = DB::table('usuarios')->insertGetId([
                'nombre'     => $alumno['nombre'],
                'ap_pat'     => $alumno['ap_pat'],
                'ap_mat'     => $alumno['ap_mat'],
                'email'      => $alumno['email'],
                'contrasenia'=> $password,
                'rol'        => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // ─── INSTITUCIONES ───────────────────────────────────────────────────
        $inst1Id = DB::table('instituciones')->insertGetId([
            'id_docente' => $docente1Id,
            'nombre'     => 'Tecnologico de Toluca',
            'logo'       => 'https://toluca.tecnm.mx/assets/logos/logo-institucional.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $inst2Id = DB::table('instituciones')->insertGetId([
            'id_docente' => $docente2Id,
            'nombre'     => 'Universidad Autonoma del Estado de Mexico',
            'logo'       => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/UAEM.svg/200px-UAEM.svg.png',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ─── RUBROS ──────────────────────────────────────────────────────────
        foreach ([$inst1Id, $inst2Id] as $instId) {
            DB::table('rubros_evaluacion')->insert([
                ['id_institucion'=>$instId,'nombre'=>'Ordinario',     'porcentaje_minimo'=>80.00,'created_at'=>$now,'updated_at'=>$now],
                ['id_institucion'=>$instId,'nombre'=>'Extraordinario','porcentaje_minimo'=>60.00,'created_at'=>$now,'updated_at'=>$now],
            ]);
        }

        // ─── GRUPOS ──────────────────────────────────────────────────────────
        $grupos = [
            // Docente 1 - TecToluca
            ['id_inst'=>$inst1Id,'id_doc'=>$docente1Id,'nombre'=>'216000','materia'=>'Auditoria',                'periodo'=>'Enero Junio 2026','no_alumnos'=>30,'codigo'=>'AUDIT01'],
            ['id_inst'=>$inst1Id,'id_doc'=>$docente1Id,'nombre'=>'216001','materia'=>'Sistemas de Informacion',  'periodo'=>'Enero Junio 2026','no_alumnos'=>25,'codigo'=>'SISINF1'],
            ['id_inst'=>$inst1Id,'id_doc'=>$docente1Id,'nombre'=>'216002','materia'=>'Base de Datos',            'periodo'=>'Enero Junio 2026','no_alumnos'=>28,'codigo'=>'BDATOS1'],
            // Docente 2 - UAEM
            ['id_inst'=>$inst2Id,'id_doc'=>$docente2Id,'nombre'=>'301A',  'materia'=>'Calculo Diferencial',      'periodo'=>'Enero Junio 2026','no_alumnos'=>35,'codigo'=>'CALCUL1'],
            ['id_inst'=>$inst2Id,'id_doc'=>$docente2Id,'nombre'=>'302B',  'materia'=>'Algebra Lineal',           'periodo'=>'Enero Junio 2026','no_alumnos'=>30,'codigo'=>'ALGEBR1'],
            ['id_inst'=>$inst2Id,'id_doc'=>$docente2Id,'nombre'=>'303C',  'materia'=>'Fisica Clasica',           'periodo'=>'Enero Junio 2026','no_alumnos'=>32,'codigo'=>'FISIC01'],
        ];

        $grupoIds = [];
        foreach ($grupos as $g) {
            $grupoIds[] = DB::table('grupos')->insertGetId([
                'id_institucion' => $g['id_inst'],
                'id_docente'     => $g['id_doc'],
                'nombre'         => $g['nombre'],
                'materia'        => $g['materia'],
                'periodo'        => $g['periodo'],
                'no_alumnos'     => $g['no_alumnos'],
                'codigo_inv'     => $g['codigo'],
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ─── INSCRIPCIONES ───────────────────────────────────────────────────
        // Alumnos 0-4 → grupos 0,1,2 (TecToluca)
        // Alumnos 5-9 → grupos 3,4,5 (UAEM)
        // Algunos alumnos en ambos grupos de su institución
        $inscripciones = [
            [$alumnoIds[0], $grupoIds[0]], [$alumnoIds[0], $grupoIds[1]],
            [$alumnoIds[1], $grupoIds[0]], [$alumnoIds[1], $grupoIds[2]],
            [$alumnoIds[2], $grupoIds[0]], [$alumnoIds[2], $grupoIds[1]], [$alumnoIds[2], $grupoIds[2]],
            [$alumnoIds[3], $grupoIds[1]], [$alumnoIds[3], $grupoIds[2]],
            [$alumnoIds[4], $grupoIds[0]], [$alumnoIds[4], $grupoIds[2]],
            [$alumnoIds[5], $grupoIds[3]], [$alumnoIds[5], $grupoIds[4]],
            [$alumnoIds[6], $grupoIds[3]], [$alumnoIds[6], $grupoIds[5]],
            [$alumnoIds[7], $grupoIds[3]], [$alumnoIds[7], $grupoIds[4]], [$alumnoIds[7], $grupoIds[5]],
            [$alumnoIds[8], $grupoIds[4]], [$alumnoIds[8], $grupoIds[5]],
            [$alumnoIds[9], $grupoIds[3]], [$alumnoIds[9], $grupoIds[5]],
        ];

        foreach ($inscripciones as $insc) {
            DB::table('grupo_alumnos')->insert([
                'id_grupo'       => $insc[1],
                'id_alumno'      => $insc[0],
                'fec_inscripcion'=> $now->toDateString(),
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ─── SESIONES Y ASISTENCIAS ──────────────────────────────────────────
        // Crear 5 sesiones cerradas para los primeros 3 grupos (TecToluca)
        // con variedad de asistencias para tener datos ricos
        $fechas = [
            Carbon::now()->subDays(20),
            Carbon::now()->subDays(15),
            Carbon::now()->subDays(10),
            Carbon::now()->subDays(5),
            Carbon::now()->subDays(2),
        ];

        // Patrones de asistencia: 1=Presente, 2=Ausente, 3=Justificada
        // Por alumno por sesión para los grupos 0,1,2
        $patronesGrupo0 = [
            // Sofia, Diego, Valeria, Andres, Camila
            $alumnoIds[0] => [1,1,1,1,1],  // 100% - perfecta
            $alumnoIds[1] => [1,1,2,1,1],  // 80%
            $alumnoIds[2] => [1,2,1,2,1],  // 60% - en riesgo
            $alumnoIds[4] => [2,2,3,1,1],  // 60% - en riesgo
        ];
        $patronesGrupo1 = [
            $alumnoIds[0] => [1,1,1,2,1],  // 80%
            $alumnoIds[2] => [1,1,1,1,2],  // 80%
            $alumnoIds[3] => [2,1,1,1,1],  // 80%
        ];
        $patronesGrupo2 = [
            $alumnoIds[1] => [1,1,1,1,1],  // 100%
            $alumnoIds[2] => [2,2,2,1,1],  // 40% - limite excedido
            $alumnoIds[3] => [1,1,2,1,3],  // 80%
            $alumnoIds[4] => [1,3,1,1,1],  // 100%
        ];

        $patronesPorGrupo = [
            $grupoIds[0] => $patronesGrupo0,
            $grupoIds[1] => $patronesGrupo1,
            $grupoIds[2] => $patronesGrupo2,
        ];

        foreach ([$grupoIds[0], $grupoIds[1], $grupoIds[2]] as $grupoId) {
            $patrones = $patronesPorGrupo[$grupoId];
            foreach ($fechas as $i => $fecha) {
                $apertura = $fecha->copy()->setHour(8)->setMinute(0);
                $cierre   = $fecha->copy()->setHour(10)->setMinute(0);

                $sesionId = DB::table('sesiones')->insertGetId([
                    'id_grupo'     => $grupoId,
                    'clave'        => null,
                    'est_sesion'   => 0, // Cerrada
                    'fec_sesion'   => $fecha->toDateString(),
                    'hora_apertura'=> $apertura,
                    'hora_cierre'  => $cierre,
                    'created_at'   => $apertura,
                    'updated_at'   => $cierre,
                ]);

                foreach ($patrones as $alumnoId => $patron) {
                    $est = $patron[$i];
                    DB::table('asistencias')->insert([
                        'id_sesion'     => $sesionId,
                        'id_alumno'     => $alumnoId,
                        'est_asistencia'=> $est,
                        'hora_registro' => $est === 1 ? $apertura->copy()->addMinutes(rand(1,15)) : null,
                        'created_at'    => $apertura,
                        'updated_at'    => $apertura,
                    ]);
                }
            }
        }

        // ─── SUSCRIPCIONES ───────────────────────────────────────────────────
        foreach ([$docente1Id, $docente2Id] as $docId) {
            DB::table('suscripciones')->insert([
                'id_usuario'     => $docId,
                'plan'           => 1, // Básico
                'est_suscripcion'=> 1, // Activa
                'fec_inicio'     => $now->toDateString(),
                'fec_fin'        => Carbon::now()->addYears(10)->toDateString(),
                'fec_ultimo_pago'=> null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->command->info('✅ OmegaSeeder completado:');
        $this->command->info('   - 2 Docentes: cmendoza@omega.com / lgutierrez@omega.com');
        $this->command->info('   - 10 Alumnos: sramirez@omega.com ... mromero@omega.com');
        $this->command->info('   - Contraseña para todos: Omega2026');
        $this->command->info('   - 6 Grupos con sesiones y asistencias variadas');
    }
}
