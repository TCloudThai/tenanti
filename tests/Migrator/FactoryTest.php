<?php namespace Orchestra\Tenanti\TestCase\Migrator;

use Illuminate\Container\Container;
use Mockery as m;
use Orchestra\Tenanti\Migrator\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::install() method.
     *
     * @test
     */
    public function testInstallMethod()
    {
        $app    = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $stub  = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runInstall]', array($app, $driver, $config));
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = array(
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        );

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runInstall')->once()->with($entity, 'foo')->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                $c($entities);
            });

        $this->assertNull($stub->install('foo'));
    }

    /**
 * Test Orchestra\Tenanti\Migrator\Factory::run() method.
 *
 * @test
 */
    public function testRunMethod()
    {
        $app    = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $stub  = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runUp]', array($app, $driver, $config));
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = array(
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        );

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runUp')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                    $c($entities);
                });

        $this->assertNull($stub->run('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::rollback() method.
     *
     * @test
     */
    public function testRollbackMethod()
    {
        $app    = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $stub  = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runDown]', array($app, $driver, $config));
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = array(
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        );

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runDown')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                    $c($entities);
                });

        $this->assertNull($stub->rollback('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::reset() method.
     *
     * @test
     */
    public function testResetMethod()
    {
        $app    = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $stub  = m::mock('\Orchestra\Tenanti\Migrator\Factory[getModel,runReset]', array($app, $driver, $config));
        $model = m::mock('\Illuminate\Database\Eloquent\Model');
        $entities = array(
            $entity = m::mock('\Illuminate\Database\Eloquent\Model'),
        );

        $stub->shouldReceive('getModel')->once()->andReturn($model)
            ->shouldReceive('runReset')->once()->with($entity, 'foo', false)->andReturnNull();
        $model->shouldReceive('newQuery->chunk')->once()->with(100, m::type('Closure'))
            ->andReturnUsing(function ($n, $c) use ($entities) {
                    $c($entities);
                });

        $this->assertNull($stub->reset('foo'));
    }

    /**
     * Test Orchestra\Tenanti\Migrator\Factory::runInstall()
     * method.
     *
     * @test
     */
    public function testRunInstallMethod()
    {
        $app    = $this->getAppContainer();
        $driver = 'user';
        $config = array();

        $app['schema']->shouldReceive('hasTable')->once()->with('user_5_migrations')->andReturn(false)
            ->shouldReceive('create')->once()->with('user_5_migrations', m::type('Closure'))->andReturnNull();

        $stub  = new Factory($app, $driver, $config);
        $model = $this->getMockModel();

        $this->assertNull($stub->runInstall($model, 'primary'));
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function getMockModel()
    {
        $model = m::mock('\Illuminate\Database\Eloquent\Model');

        $model->shouldReceive('getKey')->andReturn(5);

        return $model;
    }

    /**
     * @return \Illuminate\Container\Container
     */
    protected function getAppContainer()
    {
        $app = new Container;
        $app['db'] = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['schema'] = m::mock('\Illuminate\Database\Schema\Builder');

        $app['db']->shouldReceive('connection')->with('primary')->andReturnSelf()
            ->shouldReceive('getSchemaBuilder')->andReturn($app['schema']);

        return $app;
    }
}
