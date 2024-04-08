<?php

return [
    'controller' => '<?php

namespace App\Http\Controllers{Namespace};

use VulcanPhp\Core\Foundation\Controller;

class {ControllerName} extends Controller
{
    public function index()
    {
        // code
    }

}',
    'resource_controller' => '<?php

namespace App\Http\Controllers{Namespace};

use VulcanPhp\Core\Foundation\Controller;
use VulcanPhp\PhpRouter\Routing\Interfaces\IResource;

class {ControllerName} extends Controller implements IResource
{
    /**
     * @return mixed
     */
    public function index()
    {
        
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function show($id)
    {
        
    }

    /**
     * @return mixed
     */
    public function store()
    {
        
    }

    /**
     * @return mixed
     */
    public function create()
    {
        
    }

    /**
     * View
     * @param mixed $id
     * @return mixed
     */
    public function edit($id)
    {
        
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function update($id)
    {
        
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function destroy($id)
    {
        
    }
}',
    'seeder' => '<?php

use VulcanPhp\Core\Database\Interfaces\ISeeder;
use App\Models{Namespane};

return new class implements ISeeder
{
    public function seed(): void
    {
        $data = [];

        {ModelName}::create($data, true);
    }
};',
    'model' => '<?php

namespace App\Models{Namespace};

use VulcanPhp\SimpleDb\Model;

class {ModelName} extends Model
{
    public static function tableName(): string
    {
        return \'{TableName}\';
    }

    public static function primaryKey(): string
    {
        return \'id\';
    }

    public static function fillable(): array
    {
        return [];
    }

    public function labels(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [];
    }
}',
    'migration' => '<?php

use VulcanPhp\Core\Database\Interfaces\IMigration;
use VulcanPhp\Core\Database\Schema\Schema;

return new class implements IMigration
{
    public function up(): string
    {
        return Schema::create(\'{TableName}\')
            ->id()
            // add new fields
            ->timestamp(\'created_at\')
            ->build();
    }

    public function down(): string
    {
        return Schema::drop(\'{TableName}\');
    }
};',
    'middleware' => '<?php

namespace App\Http\Middlewares{Namespace};

use VulcanPhp\InputMaster\Request;
use VulcanPhp\InputMaster\Response;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;

class {MiddlewareName} implements IMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        // whatever you want
    }
}',
    'kernel' => '<?php

namespace App\Http\Kernels{Namespace};

use VulcanPhp\Core\Foundation\Interfaces\IKernel;

class {KernelName} implements IKernel
{
    public function boot(): void
    {   
        // configure whatever you want
    }
    
    public function shutdown(): void
    {
        // configure whatever you want
    }
}',
    'view' => '<?php

$this->layout(\'layout_name\')
    ->block(\'title\', \'Page Title Here\');
?>

<div>
    <?php echo \'Hello World\'; ?>
</div>',
    'mail' => '<?php
$this->layout(\'mail.layout\')
    ->block(\'title\', \'Mail Template Title\');

?>
<p style="color:#1e1e2d; margin:0;font-family:\'Rubik\',sans-serif;">
    <span style="font-weight:bold;font-size:32px;display: block;margin-bottom:10px;">Hello World..</span>
    <span style="font-weight:400;font-size:24px;display: block;">Write Something for this mail template</span>
</p>'
];
