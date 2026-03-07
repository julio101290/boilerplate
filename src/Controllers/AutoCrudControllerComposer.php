<?php

namespace julio101290\boilerplate\Controllers;

use CodeIgniter\Config\Services;
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Myth\Auth\Models\UserModel;

class AutoCrudControllerComposer extends BaseController
{
    use ResponseTrait;

    protected $db;
    protected $authorize;
    protected $users;
    protected $targetType = 'app'; // 'app' o 'vendor' (por defecto app)
    protected $vendorPath = '';
    protected $vendorNamespace = '';
    protected $vendorPackage = '';

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->authorize = Services::authorization();
        $this->users = new UserModel();
        helper('utilerias');
    }

    /**
     * Método principal para generar el CRUD
     *
     * @param string      $table          Nombre de la tabla
     * @param string|null $targetType     Tipo de destino: 'app' o 'vendor' (opcional, también se puede pasar por GET)
     * @param string|null $vendorPackage  Nombre del paquete vendor (ej: julio101290/boilerplatecfdidescargamasiva)
     * @param string|null $vendorNamespace Namespace exacto del paquete (opcional, se auto-detecta si no se envía)
     */
    public function index($table, $targetType = null, $vendorPackage = null, $vendorNamespace = null)
    {
        // Leer de GET si no se pasaron como argumentos
        if ($targetType === null) {
            $targetType = $this->request->getGet('target') ?? 'app';
        }
        if ($vendorPackage === null && $targetType === 'vendor') {
            $vendorPackage = $this->request->getGet('package');
        }
        if ($vendorNamespace === null && $targetType === 'vendor') {
            $vendorNamespace = $this->request->getGet('namespace');
        }

        $this->targetType = $targetType;

        if ($targetType === 'vendor' && $vendorPackage) {
            $this->setupVendorPaths($vendorPackage, $vendorNamespace);
        }

        $this->generateModel($table);
        $this->generateController($table);
        $this->generateView($table);
        $this->generateViewModal($table);
        $this->generateLanguage($table);
        $this->generateMigration($table);
        $this->generateLanguageES($table);
        
        if ($targetType === 'vendor') {
            $this->generateVendorRoutesFile($table);
            $this->updateSeederPermissions($table);
        } else {
            // Para app, seguir generando permisos directamente
            $this->generatePermissions($table);
        }

        $tableUpCase = ucfirst($table);
        $tableCamel = $tableUpCase;

        echo "<div style='background: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h3 style='color: #2c3e50;'>✅ CRUD generado exitosamente en: " . ($targetType === 'vendor' ? $this->vendorPackage : 'app') . "</h3>";

        if ($targetType === 'vendor') {
            echo $this->getVendorRoutesInstructions($table, $tableCamel);
        } else {
            echo $this->getAppRoutesInstructions($table, $tableCamel);
        }

        echo "</div>";
    }

    /**
     * Configurar rutas y namespace para vendor
     *
     * @param string $vendorPackage
     * @param string|null $vendorNamespace
     */
    protected function setupVendorPaths($vendorPackage, $vendorNamespace = null)
    {
        $this->vendorPackage = $vendorPackage;
        $this->vendorPath = ROOTPATH . "vendor/{$vendorPackage}/src/";

        if ($vendorNamespace) {
            // Si se proporciona, usar ese directamente
            $this->vendorNamespace = trim($vendorNamespace, '\\');
        } else {
            // Intentar leer el composer.json del paquete
            $composerPath = ROOTPATH . "vendor/{$vendorPackage}/composer.json";
            if (file_exists($composerPath)) {
                $composer = json_decode(file_get_contents($composerPath), true);
                if (isset($composer['autoload']['psr-4']) && is_array($composer['autoload']['psr-4'])) {
                    // Tomar el primer namespace (normalmente el principal)
                    $namespaces = array_keys($composer['autoload']['psr-4']);
                    if (!empty($namespaces)) {
                        $this->vendorNamespace = trim($namespaces[0], '\\');
                    }
                }
            }

            // Si no se encontró, generar automáticamente (conversión simple)
            if (empty($this->vendorNamespace)) {
                $parts = explode('/', $vendorPackage);
                $vendor = $parts[0];
                $package = $parts[1] ?? '';
                // Convertir kebab-case a CamelCase (ej: mi-paquete -> MiPaquete)
                $package = str_replace('-', '', ucwords($package, '-'));
                $this->vendorNamespace = $vendor . '\\' . $package;
            }
        }

        // Asegurar que el directorio vendorPath existe
        if (!is_dir($this->vendorPath)) {
            mkdir($this->vendorPath, 0777, true);
        }
        
        // Asegurar que existe el directorio Config dentro de src
        $configPath = $this->vendorPath . 'Config/';
        if (!is_dir($configPath)) {
            mkdir($configPath, 0777, true);
        }
        
        // Asegurar que existe el directorio Database/Seeds dentro de src
        $seedsPath = $this->vendorPath . 'Database/Seeds/';
        if (!is_dir($seedsPath)) {
            mkdir($seedsPath, 0777, true);
        }
    }

    /**
     * Actualizar el archivo Seeder con los permisos de la nueva tabla
     */
    protected function updateSeederPermissions($table)
    {
        $seederFile = $this->vendorPath . 'Database/Seeds/BoilerplateCFDIDescargaMasiva.php';
        $permissionName = "{$table}-permission";
        $permissionDescription = "Permiso para la tabla {$table}";
        
        // Crear el nombre de la variable para el permiso
        $permissionVar = "'{$permissionName}'";
        
        // Verificar si el archivo existe
        if (file_exists($seederFile)) {
            // Leer el contenido actual
            $content = file_get_contents($seederFile);
            
            // Verificar si el permiso ya existe para no duplicar
            if (strpos($content, $permissionVar) !== false) {
                echo "ℹ️ El permiso '{$permissionName}' ya existe en el seeder.<br>";
                return;
            }
            
            // Buscar la sección de permisos y agregar el nuevo
            $pattern = '/(\$this->authorize->createPermission\([^;]+;\s*)+/';
            if (preg_match($pattern, $content, $matches)) {
                // Ya hay permisos, agregar después del último
                $newPermissionLine = "        \$this->authorize->createPermission('{$permissionName}', '{$permissionDescription}');\n";
                $newContent = str_replace($matches[0], $matches[0] . $newPermissionLine, $content);
                file_put_contents($seederFile, $newContent);
                echo "✅ Permiso '{$permissionName}' agregado al seeder.<br>";
            } else {
                // No hay permisos, agregar después de la apertura del método run()
                $insertPoint = "public function run() {";
                $newCode = <<<PHP
public function run() {
        // Permisos para las tablas del paquete
        \$this->authorize->createPermission('{$permissionName}', '{$permissionDescription}');
PHP;
                $newContent = str_replace($insertPoint, $newCode, $content);
                file_put_contents($seederFile, $newContent);
                echo "✅ Permiso '{$permissionName}' agregado al seeder.<br>";
            }
            
            // También asegurar que se asigna al usuario 1
            $assignPattern = '/(\$this->authorize->addPermissionToUser\([^;]+;\s*)+/';
            $assignLine = "        \$this->authorize->addPermissionToUser('{$permissionName}', 1);\n";
            
            if (preg_match($assignPattern, $content, $assignMatches)) {
                // Ya hay asignaciones, agregar después del último
                $newContent = file_get_contents($seederFile); // Recargar por si cambió
                $newContent = str_replace($assignMatches[0], $assignMatches[0] . $assignLine, $newContent);
                file_put_contents($seederFile, $newContent);
                echo "✅ Asignación del permiso '{$permissionName}' al usuario 1 agregada.<br>";
            } else {
                // Buscar el final del método run() para agregar asignaciones
                $newContent = file_get_contents($seederFile);
                $endPattern = '/\s*}\s*public function down/';
                if (preg_match($endPattern, $newContent, $endMatches)) {
                    $newContent = str_replace($endMatches[0], "\n" . $assignLine . "    }" . "\n\n    public function down", $newContent);
                    file_put_contents($seederFile, $newContent);
                    echo "✅ Asignación del permiso '{$permissionName}' al usuario 1 agregada.<br>";
                }
            }
            
        } else {
            // Crear el archivo seeder desde cero
            $this->createSeederFile($table, $permissionName, $permissionDescription);
        }
    }

    /**
     * Crear archivo seeder nuevo
     */
    protected function createSeederFile($table, $permissionName, $permissionDescription)
    {
        $namespace = $this->vendorNamespace . '\\Database\\Seeds';
        $seederPath = $this->vendorPath . 'Database/Seeds/BoilerplateCFDIDescargaMasiva.php';
        
        $seederContent = <<<PHP
<?php

namespace {$namespace};

use CodeIgniter\Config\Services;
use CodeIgniter\Database\Seeder;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;

/**
 * Class BoilerplateSeeder.
 */
class BoilerplateCFDIDescargaMasiva extends Seeder {

    /**
     * @var Authorize
     */
    protected \$authorize;

    /**
     * @var Db
     */
    protected \$db;

    /**
     * @var Users
     */
    protected \$users;

    public function __construct() {
        \$this->authorize = Services::authorization();
        \$this->db = \Config\Database::connect();
        \$this->users = new UserModel();
    }

    public function run() {
        // Permisos para las tablas del paquete
        \$this->authorize->createPermission('{$permissionName}', '{$permissionDescription}');
        
        // Asignar permisos al usuario administrador (ID 1)
        \$this->authorize->addPermissionToUser('{$permissionName}', 1);
    }

    public function down() {
        // Para eliminar permisos si es necesario
        // \$this->authorize->deletePermission('{$permissionName}');
    }
}
PHP;

        file_put_contents($seederPath, $seederContent);
        echo "✅ Archivo seeder creado con permiso '{$permissionName}': <code>{$seederPath}</code><br>";
    }

    /**
     * Generar archivo de rutas para el paquete vendor
     */
    protected function generateVendorRoutesFile($table)
    {
        $tableUpCase = ucfirst($table);
        $namespace = $this->vendorNamespace . '\\Controllers';
        $routesPath = $this->vendorPath . 'Config/Routes.php';
        
        if (file_exists($routesPath)) {
            // Leer el contenido existente
            $existingContent = file_get_contents($routesPath);
            
            // Verificar si ya existe la ruta para esta tabla para no duplicar
            if (strpos($existingContent, "resource('{$table}'") !== false) {
                echo "ℹ️ Las rutas para '{$table}' ya existen en el archivo de rutas del paquete.<br>";
                return;
            }
            
            // Buscar el cierre del grupo 'admin' para insertar antes
            $pattern = '/\$routes->group\(\'admin\', function \(\$routes\) \{([^}]*)\}\);/s';
            if (preg_match($pattern, $existingContent, $matches)) {
                // Ya existe el grupo admin, agregar dentro
                $groupContent = $matches[1];
                $newGroupContent = $groupContent . "\n\n    // Rutas para {$table}\n" . $this->getRoutesGroupContent($table, $tableUpCase, $namespace, '    ');
                $newContent = str_replace($groupContent, $newGroupContent, $existingContent);
                file_put_contents($routesPath, $newContent);
                echo "✅ Rutas para '{$table}' agregadas al archivo de rutas existente.<br>";
            } else {
                // No existe el grupo admin, agregarlo al final
                $newContent = $existingContent . "\n\n" . $this->getVendorRoutesCode($table, $tableUpCase, $namespace);
                file_put_contents($routesPath, $newContent);
                echo "✅ Grupo admin con rutas para '{$table}' agregado al archivo de rutas.<br>";
            }
        } else {
            // Crear archivo nuevo
            $content = "<?php\n\n";
            $content .= "// Rutas para el paquete {$this->vendorPackage}\n";
            $content .= $this->getVendorRoutesCode($table, $tableUpCase, $namespace);
            file_put_contents($routesPath, $content);
            echo "✅ Archivo de rutas del paquete creado: <code>{$routesPath}</code><br>";
        }
    }

    /**
     * Obtener el código PHP para las rutas del vendor
     */
    protected function getVendorRoutesCode($table, $tableUpCase, $namespace)
    {
        $groupContent = $this->getRoutesGroupContent($table, $tableUpCase, $namespace, '    ');
        
        return <<<PHP
\$routes->group('admin', function (\$routes) {{$groupContent}
});
PHP;
    }

    /**
     * Obtener el contenido del grupo de rutas
     */
    protected function getRoutesGroupContent($table, $tableUpCase, $namespace, $indent = '    ')
    {
        return <<<PHP

{$indent}// Rutas para {$table}
{$indent}\$routes->resource('{$table}', [
{$indent}    'filter' => 'permission:{$table}-permission',
{$indent}    'controller' => '{$tableUpCase}Controller',
{$indent}    'except' => 'show',
{$indent}    'namespace' => '{$namespace}',
{$indent}]);

{$indent}\$routes->post('{$table}/save'
{$indent}        , '{$tableUpCase}Controller::save'
{$indent}        , ['namespace' => '{$namespace}', 'filter' => 'csrf']
{$indent});

{$indent}\$routes->post('{$table}/get{$tableUpCase}'
{$indent}        , '{$tableUpCase}Controller::get{$tableUpCase}'
{$indent}        , ['namespace' => '{$namespace}']
{$indent});
PHP;
    }

    /**
     * Generar instrucciones para rutas en App
     */
    protected function getAppRoutesInstructions($table, $tableUpCase)
    {
        return <<<HTML
<h4>📁 Rutas para agregar en <code>app/Config/Routes.php</code>:</h4>
<pre style="background: #f4f4f4; padding: 15px; border-radius: 8px; overflow-x: auto;">
use CodeIgniter\Exceptions\PageNotFoundException;

// Rutas para {$table}
\$routes->resource('{$table}', [
    'filter' => 'permission:{$table}-permission',
    'controller' => '{$tableUpCase}Controller',
    'except' => 'show'
]);

// Rutas POST con protección CSRF
\$routes->post('{$table}/save', '{$tableUpCase}Controller::save', ['filter' => 'csrf']);
\$routes->post('{$table}/get{$tableUpCase}', '{$tableUpCase}Controller::get{$tableUpCase}');
</pre>
HTML;
    }

    /**
     * Generar instrucciones para cargar rutas del vendor
     */
    protected function getVendorRoutesInstructions($table, $tableUpCase)
    {
        $routesFilePath = "vendor/{$this->vendorPackage}/src/Config/Routes.php";
        $seederFilePath = "vendor/{$this->vendorPackage}/src/Database/Seeds/BoilerplateCFDIDescargaMasiva.php";
        
        return <<<HTML
<h4>📦 Instrucciones para el paquete vendor:</h4>

<p><strong>✅ Se ha creado/actualizado el archivo de rutas en:</strong><br>
<code>{$routesFilePath}</code></p>

<p><strong>✅ Se ha actualizado el archivo seeder con el permiso:</strong><br>
<code>{$seederFilePath}</code></p>

<p><strong>Para activar las rutas, agrega esta línea en <code>app/Config/Routes.php</code>:</strong></p>
<pre style="background: #f4f4f4; padding: 15px; border-radius: 8px; overflow-x: auto;">
// Cargar rutas del paquete {$this->vendorPackage}
require_once ROOTPATH . '{$routesFilePath}';
</pre>

<p><strong>Para instalar los permisos, ejecuta el seeder:</strong></p>
<pre style="background: #f4f4f4; padding: 15px; border-radius: 8px;">
php spark db:seed BoilerplateCFDIDescargaMasiva
</pre>

<p><strong>Registrar namespace en <code>app/Config/Autoload.php</code> (si no está ya):</strong></p>
<pre style="background: #f4f4f4; padding: 15px; border-radius: 8px;">
public \$psr4 = [
    '{$this->vendorNamespace}' => ROOTPATH . 'vendor/{$this->vendorPackage}/src',
    // ... otros namespaces
];
</pre>
HTML;
    }

    /**
     * Obtener la ruta base según el tipo de destino
     */
    protected function getBasePath($type)
    {
        if ($this->targetType === 'vendor') {
            $paths = [
                'Models'     => $this->vendorPath . 'Models/',
                'Controllers'=> $this->vendorPath . 'Controllers/',
                'Views'      => $this->vendorPath . 'Views/',
                'Language'   => $this->vendorPath . 'Language/',
                'Database'   => $this->vendorPath . 'Database/Migrations/',
                'Config'     => $this->vendorPath . 'Config/',
                'Seeds'      => $this->vendorPath . 'Database/Seeds/',
            ];
            return $paths[$type] ?? $this->vendorPath . $type . '/';
        } else {
            $paths = [
                'Models'     => ROOTPATH . 'app/Models/',
                'Controllers'=> ROOTPATH . 'app/Controllers/',
                'Views'      => ROOTPATH . 'app/Views/',
                'Language'   => ROOTPATH . 'app/Language/',
                'Database'   => ROOTPATH . 'app/Database/Migrations/',
            ];
            return $paths[$type] ?? ROOTPATH . 'app/' . $type . '/';
        }
    }

    /**
     * Obtener el namespace para las clases
     */
    protected function getNamespace($type)
    {
        if ($this->targetType === 'vendor') {
            $namespaces = [
                'Model'      => "{$this->vendorNamespace}\\Models",
                'Controller' => "{$this->vendorNamespace}\\Controllers",
            ];
            return $namespaces[$type] ?? $this->vendorNamespace;
        } else {
            $namespaces = [
                'Model'      => "App\\Models",
                'Controller' => "App\\Controllers",
            ];
            return $namespaces[$type] ?? "App";
        }
    }

    /**
     * Generar el archivo del modelo
     */
    public function generateModel(string $table): void
    {
        $forge = $this->db;

        if (!$forge->tableExists($table)) {
            echo "❌ La tabla '$table' no existe.";
            return;
        }

        $fieldsArray = $forge->getFieldNames($table);

        if (empty($fieldsArray)) {
            echo "❌ No se pudieron obtener los campos de la tabla '$table'.";
            return;
        }

        $allowedFields = array_map(fn($f) => "'$f'", $fieldsArray);
        $allowedFieldsStr = implode(', ', $allowedFields);

        $prefixedFields = array_map(fn($f) => "a.$f", $fieldsArray);
        $selectFieldsStr = implode(', ', $prefixedFields);

        $nombreClase = ucfirst($table) . "Model";
        $nombreTabla = ucfirst($table);
        $namespace = $this->getNamespace('Model');

        $modelCode = <<<PHP
<?php

namespace {$namespace};

use CodeIgniter\Model;

class {$nombreClase} extends Model
{
    protected \$table            = '{$table}';
    protected \$primaryKey       = 'id';
    protected \$useAutoIncrement = true;
    protected \$returnType       = 'array';
    protected \$useSoftDeletes   = true;
    protected \$allowedFields    = [{$allowedFieldsStr}];
    protected \$useTimestamps    = true;
    protected \$createdField     = 'created_at';
    protected \$updatedField     = 'updated_at';
    protected \$deletedField     = 'deleted_at';

    protected \$validationRules    = [];
    protected \$validationMessages = [];
    protected \$skipValidation     = false;

    /**
     * Obtiene los registros filtrando por empresas del usuario
     *
     * @param array \$idEmpresas
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function mdlGet{$nombreTabla}(array \$idEmpresas)
    {
        return \$this->db->table('{$table} a')
            ->join('empresas b', 'a.idEmpresa = b.id')
            ->select("{$selectFieldsStr}, b.nombre AS nombreEmpresa")
            ->whereIn('a.idEmpresa', \$idEmpresas);
    }
}
PHP;

        $basePath = $this->getBasePath('Models');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $filePath = $basePath . "{$nombreClase}.php";
        file_put_contents($filePath, $modelCode);

        echo "✅ Modelo generado: <code>{$filePath}</code><br>";
    }

    /**
     * Generar el archivo del controlador
     */
    public function generateController(string $table): void
    {
        $fieldsArray = $this->db->getFieldNames($table);

        if (empty($fieldsArray)) {
            echo "❌ No se pudieron obtener los campos de la tabla '$table'.";
            return;
        }

        $nameClassModel = ucfirst($table) . "Model";
        $tableUpCase = ucfirst($table);
        $varName = lcfirst($table);
        $namespace = $this->getNamespace('Controller');
        $modelNamespace = $this->getNamespace('Model');

        // Determinar los use statements según el destino
        if ($this->targetType === 'vendor') {
            $useStatements = <<<USE
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
USE;
        } else {
            $useStatements = <<<USE
use App\Controllers\BaseController;
use App\Models\\{$nameClassModel};
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
USE;
        }

        // Definir la ruta de la vista para vendor (con namespace)
        $viewPath = $this->targetType === 'vendor' 
            ? "'{$this->vendorNamespace}\\Views\\{$table}'" 
            : "'{$table}'";

        $controllerCode = <<<PHP
<?php

namespace {$namespace};

{$useStatements}

class {$tableUpCase}Controller extends BaseController
{
    use ResponseTrait;

    protected \$log;
    protected \${$varName};
    protected \$empresa;

    public function __construct()
    {
        \$this->{$varName} = new \\{$modelNamespace}\\{$nameClassModel}();
        \$this->log        = new LogModel();
        \$this->empresa    = new EmpresasModel();
        helper(['menu', 'utilerias']);
    }

    /**
     * Muestra la vista principal y procesa solicitudes AJAX para DataTables
     */
    public function index()
    {
        helper('auth');

        \$idUser = user()->id;
        \$titulos["empresas"] = \$this->empresa->mdlEmpresasPorUsuario(\$idUser);
        \$empresasID = count(\$titulos["empresas"]) === 0 ? [0] : array_column(\$titulos["empresas"], "id");

        if (\$this->request->isAJAX()) {
            \$request = service('request');

            \$draw           = (int) \$request->getGet('draw');
            \$start          = (int) \$request->getGet('start');
            \$length         = (int) \$request->getGet('length');
            \$searchValue    = \$request->getGet('search')['value'] ?? '';
            \$orderColumnIndex = (int) \$request->getGet('order')[0]['column'] ?? 0;
            \$orderDir       = \$request->getGet('order')[0]['dir'] ?? 'asc';

            \$fields = \$this->{$varName}->allowedFields;
            \$orderField = \$fields[\$orderColumnIndex] ?? 'id';

            \$builder = \$this->{$varName}->mdlGet{$tableUpCase}(\$empresasID);

            \$total = clone \$builder;
            \$recordsTotal = \$total->countAllResults(false);

            if (!empty(\$searchValue)) {
                \$builder->groupStart();
                foreach (\$fields as \$field) {
                    \$builder->orLike("a." . \$field, \$searchValue);
                }
                \$builder->groupEnd();
            }

            \$filteredBuilder = clone \$builder;
            \$recordsFiltered = \$filteredBuilder->countAllResults(false);

            \$data = \$builder->orderBy("a." . \$orderField, \$orderDir)
                             ->get(\$length, \$start)
                             ->getResultArray();

            return \$this->response->setJSON([
                'draw'            => \$draw,
                'recordsTotal'    => \$recordsTotal,
                'recordsFiltered' => \$recordsFiltered,
                'data'            => \$data,
            ]);
        }

        \$titulos["title"]    = lang('{$table}.title');
        \$titulos["subtitle"] = lang('{$table}.subtitle');
        return view({$viewPath}, \$titulos);
    }

    /**
     * Obtiene un registro específico para edición (vía AJAX)
     */
    public function get{$tableUpCase}()
    {
        helper('auth');

        \$idUser = user()->id;
        \$titulos["empresas"] = \$this->empresa->mdlEmpresasPorUsuario(\$idUser);
        \$empresasID = count(\$titulos["empresas"]) === 0 ? [0] : array_column(\$titulos["empresas"], "id");

        \$id{$tableUpCase} = \$this->request->getPost("id{$tableUpCase}");
        \$dato = \$this->{$varName}->whereIn('idEmpresa', \$empresasID)
                                   ->where('id', \$id{$tableUpCase})
                                   ->first();

        return \$this->response->setJSON(\$dato);
    }

    /**
     * Guarda o actualiza un registro (con protección CSRF automática)
     */
    public function save()
    {
        helper('auth');

        \$userName = user()->username;
        \$datos = \$this->request->getPost();
        \$idKey = \$datos["id{$tableUpCase}"] ?? 0;

        if (\$idKey == 0) {
            try {
                if (!\$this->{$varName}->save(\$datos)) {
                    \$errores = implode(" ", \$this->{$varName}->errors());
                    return \$this->respond(['status' => 400, 'message' => \$errores], 400);
                }
                \$this->log->save([
                    "description" => lang("{$table}.logDescription") . json_encode(\$datos),
                    "user"        => \$userName
                ]);
                return \$this->respond(['status' => 201, 'message' => 'Guardado correctamente'], 201);
            } catch (\Throwable \$ex) {
                return \$this->respond(['status' => 500, 'message' => 'Error al guardar: ' . \$ex->getMessage()], 500);
            }
        } else {
            if (!\$this->{$varName}->update(\$idKey, \$datos)) {
                \$errores = implode(" ", \$this->{$varName}->errors());
                return \$this->respond(['status' => 400, 'message' => \$errores], 400);
            }
            \$this->log->save([
                "description" => lang("{$table}.logUpdated") . json_encode(\$datos),
                "user"        => \$userName
            ]);
            return \$this->respond(['status' => 200, 'message' => 'Actualizado correctamente'], 200);
        }
    }

    /**
     * Elimina un registro (soft delete)
     */
    public function delete(\$id)
    {
        helper('auth');

        \$userName = user()->username;
        \$registro = \$this->{$varName}->find(\$id);

        if (!\$this->{$varName}->delete(\$id)) {
            return \$this->respond(['status' => 404, 'message' => lang("{$table}.msg.msg_get_fail")], 404);
        }

        \$this->{$varName}->purgeDeleted();
        \$this->log->save([
            "description" => lang("{$table}.logDeleted") . json_encode(\$registro),
            "user"        => \$userName
        ]);

        return \$this->respondDeleted(\$registro, lang("{$table}.msg_delete"));
    }
}
PHP;

        $basePath = $this->getBasePath('Controllers');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $filePath = $basePath . "{$tableUpCase}Controller.php";
        file_put_contents($filePath, $controllerCode);

        echo "✅ Controlador generado: <code>{$filePath}</code><br>";
    }

    /**
     * Genera la vista principal (listado)
     */
    public function generateView($table)
    {
        $tableUpCase = ucfirst($table);
        $query = $this->db->getFieldNames($table);

        $columnaDatatable = "";
        $datosDatatable   = "";
        $variablesGuardar1 = "";
        $variablesFormData = "";
        $inputsEdit       = "";
        $contador = 0;

        foreach ($query as $field) {
            if ($contador > 0) {
                $columnaDatatable .= "                            <th><?= lang('{$table}.fields.{$field}') ?></th>\n";
                $datosDatatable   .= "            { 'data': '{$field}' },\n";

                if (!in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                    $variablesGuardar1 .= "        var {$field} = $(\"#{$field}\").val();\n";
                    $variablesFormData .= "        datos.append(\"{$field}\", {$field});\n";

                    if ($field === 'idEmpresa') {
                        $inputsEdit .= "            $(\"#{$field}\").val(respuesta[\"{$field}\"]).trigger(\"change\");\n";
                    } else {
                        $inputsEdit .= "            $(\"#{$field}\").val(respuesta[\"{$field}\"]);\n";
                    }
                }
            } else {
                $variablesGuardar1 = "        var id{$tableUpCase} = $(\"#id{$tableUpCase}\").val();\n";
                $variablesFormData = "        var datos = new FormData();\n";
                $variablesFormData .= "        datos.append(\"id{$tableUpCase}\", id{$tableUpCase});\n";
            }
            $contador++;
        }

        // Token CSRF y configuración AJAX
        $csrfMeta = '<meta name="csrf-token" content="<?= csrf_hash() ?>">' . "\n";
        $csrfScript = <<<JS
    // Configuración global de AJAX para incluir el token CSRF en las cabeceras
    \$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': \$('meta[name="csrf-token"]').attr('content')
        }
    });
JS;

        $viewPath = $this->getBasePath('Views') . "{$table}.php";
        $viewContent = $this->generateViewContent(
            $table,
            $tableUpCase,
            $columnaDatatable,
            $datosDatatable,
            $variablesGuardar1,
            $variablesFormData,
            $inputsEdit,
            $contador,
            $csrfMeta,
            $csrfScript
        );

        if (!is_dir(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0777, true);
        }

        file_put_contents($viewPath, $viewContent);
        echo "✅ Vista principal generada: <code>{$viewPath}</code><br>";
    }

    /**
     * Construye el contenido de la vista principal
     */
    protected function generateViewContent($table, $tableUpCase, $columnaDatatable, $datosDatatable,
                                           $variablesGuardar1, $variablesFormData, $inputsEdit,
                                           $contador, $csrfMeta, $csrfScript)
    {
        // Determinar la ruta de inclusión del modal
        if ($this->targetType === 'vendor') {
            // Para vendor: usar namespace con barras invertidas
            $modalInclude = "{$this->vendorNamespace}\\Views\\modules{$tableUpCase}\\modalCapture{$tableUpCase}";
            // Reemplazar \ por / para que funcione en la sintaxis de include?
            // En CodeIgniter, el helper view() acepta tanto / como \ pero es más seguro usar \\ en el código PHP.
            // Para la salida en la vista, usamos \\ (doble barra invertida) porque se imprimirá como texto.
            $modalInclude = str_replace('\\', '\\\\', $modalInclude);
        } else {
            $modalInclude = "modules{$tableUpCase}/modalCapture{$tableUpCase}";
        }

        $ajaxSuccessCheck = <<<JS
            if (respuesta?.message?.includes("Guardado") || respuesta?.message?.includes("Actualizado")) {
                Toast.fire({
                    icon: 'success',
                    title: respuesta.message
                });
                table{$tableUpCase}.ajax.reload();
                $("#btnSave{$tableUpCase}").removeAttr("disabled");
                $('#modalAdd{$tableUpCase}').modal('hide');
            } else {
                Toast.fire({
                    icon: 'error',
                    title: respuesta.message || "Error desconocido"
                });
                $("#btnSave{$tableUpCase}").removeAttr("disabled");
            }
JS;

        return <<<HTML
<?= \$this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= \$this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= \$this->include('julio101290\boilerplate\Views\load\\nestable') ?>
<?= \$this->extend('julio101290\boilerplate\Views\layout\index') ?>
{$csrfMeta}
<?= \$this->section('content') ?>
<?= \$this->include('{$modalInclude}') ?>

<div class="card card-default">
    <div class="card-header">
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-primary btnAdd{$tableUpCase}" data-toggle="modal" data-target="#modalAdd{$tableUpCase}">
                    <i class="fa fa-plus"></i> <?= lang('{$table}.add') ?>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="table{$tableUpCase}" class="table table-striped table-hover va-middle table{$tableUpCase}">
                        <thead>
                            <tr>
                                <th>#</th>
                                {$columnaDatatable}
                                <th><?= lang('{$table}.fields.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= \$this->endSection() ?>

<?= \$this->section('js') ?>
<script>
{$csrfScript}

var table{$tableUpCase} = $('#table{$tableUpCase}').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    order: [[1, 'asc']],
    ajax: {
        url: '<?= base_url('admin/{$table}') ?>',
        method: 'GET',
        dataType: "json"
    },
    columnDefs: [{
        orderable: false,
        targets: [{$contador}],
        searchable: false,
        targets: [{$contador}]
    }],
    columns: [
        { 'data': 'id' },
        {$datosDatatable}
        {
            "data": function (data) {
                return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-warning btnEdit{$tableUpCase}" data-toggle="modal" id{$tableUpCase}="\${data.id}" data-target="#modalAdd{$tableUpCase}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-delete" data-id="\${data.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>`;
            }
        }
    ]
});

$(document).on('click', '#btnSave{$tableUpCase}', function (e) {
    e.preventDefault();
    {$variablesGuardar1}
    $("#btnSave{$tableUpCase}").attr("disabled", true);
    {$variablesFormData}
    $.ajax({
        url: "<?= base_url('admin/{$table}/save') ?>",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            {$ajaxSuccessCheck}
        },
        error: function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: jqXHR.responseText
            });
            $("#btnSave{$tableUpCase}").removeAttr("disabled");
        }
    });
});

$(".table{$tableUpCase}").on("click", ".btnEdit{$tableUpCase}", function () {
    var id{$tableUpCase} = $(this).attr("id{$tableUpCase}");
    var datos = new FormData();
    datos.append("id{$tableUpCase}", id{$tableUpCase});
    $.ajax({
        url: "<?= base_url('admin/{$table}/get{$tableUpCase}') ?>",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (respuesta) {
            $("#id{$tableUpCase}").val(respuesta["id"]);
            {$inputsEdit}
        }
    });
});

$(".table{$tableUpCase}").on("click", ".btn-delete", function () {
    var id{$tableUpCase} = $(this).attr("data-id");
    Swal.fire({
        title: '<?= lang('boilerplate.global.sweet.title') ?>',
        text: "<?= lang('boilerplate.global.sweet.text') ?>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: `<?= base_url('admin/{$table}') ?>/` + id{$tableUpCase},
                method: 'DELETE',
            }).done((data, textStatus, jqXHR) => {
                Toast.fire({
                    icon: 'success',
                    title: jqXHR.statusText,
                });
                table{$tableUpCase}.ajax.reload();
            }).fail((error) => {
                Toast.fire({
                    icon: 'error',
                    title: error.responseJSON.messages.error,
                });
            });
        }
    });
});

$(function () {
    $("#modalAdd{$tableUpCase}").draggable();
});
</script>
<?= \$this->endSection() ?>
HTML;
    }

    /**
     * Genera la vista del modal (formulario)
     */
    public function generateViewModal($table)
    {
        $tableUpCase = ucfirst($table);
        $query = $this->db->getFieldNames($table);

        $campos = "";
        $contador = 0;
        foreach ($query as $field) {
            if ($contador > 0 && !in_array($field, ['created_at', 'updated_at', 'deleted_at'])) {
                if ($field === 'idEmpresa') {
                    $campos .= $this->generateEmpresaField($table);
                } elseif ($field === 'idSucursal') {
                    $campos .= $this->generateSucursalField($table);
                } else {
                    $campos .= $this->generateTextField($table, $field);
                }
            }
            $contador++;
        }

        $viewModalPath = $this->getBasePath('Views') . "modules{$tableUpCase}/";
        if (!is_dir($viewModalPath)) {
            mkdir($viewModalPath, 0777, true);
        }

        // Para vendor, la inclusión se hará con namespace en la vista principal, 
        // el contenido del modal no necesita cambios adicionales.

        $modalContent = <<<HTML
<!-- Modal {$tableUpCase} -->
<div class="modal fade" id="modalAdd{$tableUpCase}" tabindex="-1" role="dialog" aria-labelledby="modalAdd{$tableUpCase}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('{$table}.createEdit') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-{$table}" class="form-horizontal">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id{$tableUpCase}" name="id{$tableUpCase}" value="0">

                    {$campos}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSave{$tableUpCase}"><?= lang('boilerplate.global.save') ?></button>
            </div>
        </div>
    </div>
</div>

<?= \$this->section('js') ?>
<script>
    $(document).on('click', '.btnAdd{$tableUpCase}', function (e) {
        $(".form-control").val("");
        $("#id{$tableUpCase}").val("0");
        $("#btnSave{$tableUpCase}").removeAttr("disabled");
    });

    $(document).on('click', '.btnEdit{$tableUpCase}', function (e) {
        var id{$tableUpCase} = $(this).attr("id{$tableUpCase}");
        $(".form-control").val("");
        $("#id{$tableUpCase}").val(id{$tableUpCase});
        $("#btnSave{$tableUpCase}").removeAttr("disabled");
    });

    $("#idEmpresa").select2();
</script>
<?= \$this->endSection() ?>
HTML;

        file_put_contents($viewModalPath . "modalCapture{$tableUpCase}.php", $modalContent);
        echo "✅ Modal generado: <code>{$viewModalPath}modalCapture{$tableUpCase}.php</code><br>";
    }

    protected function generateEmpresaField($table)
    {
        return <<<HTML
                    <div class="form-group row">
                        <label for="idEmpresa" class="col-sm-2 col-form-label">Empresa</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <select class="form-control idEmpresa" name="idEmpresa" id="idEmpresa" style="width:80%;">
                                    <option value="0">Seleccione empresa</option>
                                    <?php
                                    foreach (\$empresas as \$key => \$value) {
                                        echo "<option value='\$value[id]' selected>\$value[id] - \$value[nombre] </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
HTML;
    }

    protected function generateSucursalField($table)
    {
        return <<<HTML
                    <div class="form-group row">
                        <label for="sucursal" class="col-sm-2 col-form-label">Sucursal</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <select class="form-control sucursal" name="sucursal" id="sucursal" style="width:80%;">
                                    <option value="0">Seleccione sucursal</option>
                                </select>
                            </div>
                        </div>
                    </div>
HTML;
    }

    protected function generateTextField($table, $field)
    {
        return <<<HTML
                    <div class="form-group row">
                        <label for="{$field}" class="col-sm-2 col-form-label"><?= lang('{$table}.fields.{$field}') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="{$field}" id="{$field}" class="form-control <?= session('error.{$field}') ? 'is-invalid' : '' ?>" value="<?= old('{$field}') ?>" placeholder="<?= lang('{$table}.fields.{$field}') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
HTML;
    }

    /**
     * Genera archivo de idioma inglés
     */
    public function generateLanguage($table)
    {
        $tableUpCase = ucfirst($table);
        $query = $this->db->getFieldNames($table);

        $campos = "";
        foreach ($query as $index => $field) {
            if ($index > 0) {
                $campos .= "\${$table}[\"fields\"][\"{$field}\"] = \"" . ucfirst(str_replace('_', ' ', $field)) . "\";\n";
            }
        }

        $langCode = <<<PHP
<?php

\${$table}["logDescription"] = "The {$table} was saved with the following data:";
\${$table}["logUpdate"]      = "The {$table} was updated with the following data:";
\${$table}["logDeleted"]     = "The {$table} was deleted with the following data:";
\${$table}["msg_delete"]     = "The {$table} was deleted successfully:";

\${$table}["add"]           = "Add {$tableUpCase}";
\${$table}["edit"]          = "Edit {$table}";
\${$table}["createEdit"]    = "Create / Edit";
\${$table}["title"]         = "{$table} Management";
\${$table}["subtitle"]      = "{$table} List";

{$campos}
\${$table}["fields"]["actions"] = "Actions";

\${$table}["msg"]["msg_insert"]     = "The {$table} has been successfully added.";
\${$table}["msg"]["msg_update"]     = "The {$table} has been successfully modified.";
\${$table}["msg"]["msg_delete"]     = "The {$table} has been successfully deleted.";
\${$table}["msg"]["msg_get"]        = "The {$tableUpCase} has been successfully retrieved.";
\${$table}["msg"]["msg_get_fail"]   = "The {$table} not found or already deleted.";

return \${$table};
PHP;

        $basePath = $this->getBasePath('Language') . "en/";
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        file_put_contents($basePath . "{$table}.php", $langCode);
        echo "✅ Idioma EN generado: <code>{$basePath}{$table}.php</code><br>";
    }

    /**
     * Genera archivo de idioma español
     */
    public function generateLanguageES($table)
    {
        $tableUpCase = ucfirst($table);
        $query = $this->db->getFieldNames($table);

        $campos = "";
        foreach ($query as $index => $field) {
            if ($index > 0) {
                $campos .= "\${$table}[\"fields\"][\"{$field}\"] = \"" . ucfirst(str_replace('_', ' ', $field)) . "\";\n";
            }
        }

        $langCode = <<<PHP
<?php

\${$table}["logDescription"] = "El registro en {$table} fue guardado con los siguientes datos:";
\${$table}["logUpdate"]      = "El registro en {$table} fue actualizado con los siguientes datos:";
\${$table}["logDeleted"]     = "El registro en {$table} fue eliminado con los siguientes datos:";
\${$table}["msg_delete"]     = "El registro en {$table} fue eliminado correctamente:";

\${$table}["add"]           = "Agregar {$tableUpCase}";
\${$table}["edit"]          = "Editar {$table}";
\${$table}["createEdit"]    = "Crear / Editar";
\${$table}["title"]         = "Administración de {$table}";
\${$table}["subtitle"]      = "Lista de {$table}";

{$campos}
\${$table}["fields"]["actions"] = "Acciones";

\${$table}["msg"]["msg_insert"]     = "Registro agregado correctamente.";
\${$table}["msg"]["msg_update"]     = "Registro modificado correctamente.";
\${$table}["msg"]["msg_delete"]     = "Registro eliminado correctamente.";
\${$table}["msg"]["msg_get"]        = "Registro obtenido correctamente.";
\${$table}["msg"]["msg_get_fail"]   = "Registro no encontrado o eliminado.";

return \${$table};
PHP;

        $basePath = $this->getBasePath('Language') . "es/";
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        file_put_contents($basePath . "{$table}.php", $langCode);
        echo "✅ Idioma ES generado: <code>{$basePath}{$table}.php</code><br>";
    }

    /**
     * Genera archivo de migración
     */
    public function generateMigration($table)
    {
        $tableUpCase = ucfirst($table);
        $query = $this->db->getFieldData($table);

        $campos = "";
        $contador = 0;
        foreach ($query as $field) {
            if ($contador == 0) {
                $campos .= "            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],\n";
            } else {
                $nullable = $field->nullable ? 'true' : 'false';
                if (!in_array($field->name, ['created_at', 'updated_at', 'deleted_at'])) {
                    $campos .= "            '{$field->name}' => ['type' => '{$field->type}', 'constraint' => {$field->max_length}, 'null' => {$nullable}],\n";
                }
            }
            $contador++;
        }

        $namespace = $this->targetType === 'vendor'
            ? "{$this->vendorNamespace}\\Database\\Migrations"
            : "App\\Database\\Migrations";

        $migrationCode = <<<PHP
<?php

namespace {$namespace};

use CodeIgniter\Database\Migration;

class {$tableUpCase} extends Migration
{
    public function up()
    {
        \$this->forge->addField([
{$campos}
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        \$this->forge->addKey('id', true);
        \$this->forge->createTable('{$table}', true);
    }

    public function down()
    {
        \$this->forge->dropTable('{$table}', true);
    }
}
PHP;

        $basePath = $this->getBasePath('Database');
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        $fechaActual = date('Y-m-d-His');
        $filePath = $basePath . "{$fechaActual}_{$tableUpCase}.php";
        file_put_contents($filePath, $migrationCode);

        echo "✅ Migración generada: <code>{$filePath}</code><br>";
    }

    /**
     * Genera permisos (solo para app, en vendor se usa el seeder)
     */
    public function generatePermissions($table)
    {
        if ($this->targetType === 'app') {
            $permiso = "{$table}-permission";
            $infoPermiso = $this->authorize->permission($permiso);

            if ($infoPermiso === null) {
                $this->authorize->createPermission($permiso, 'Permiso para la lista de ' . $table);
                $this->authorize->addPermissionToGroup($permiso, 'admin');
                $this->authorize->addPermissionToUser($permiso, 1);
                echo "✅ Permiso '{$permiso}' creado y asignado.<br>";
            } else {
                echo "ℹ️ El permiso '{$permiso}' ya existe.<br>";
            }
        }
    }
}