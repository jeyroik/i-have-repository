<?php

use jeyroik\components\repositories\plugins\RepoPluginUuid;
use jeyroik\components\repositories\RepositoryFile;
use jeyroik\components\repositories\THasRepository;
use jeyroik\interfaces\repositories\IRepository;
use PHPUnit\Framework\TestCase;
use tests\Some;

class RepsoitoryTest extends TestCase
{
    use THasRepository;

    public function testBasic()
    {
        putenv('REPOSITORY__PLUGINS_FILE=/tmp/plugins.php');
        file_put_contents('/tmp/plugins.php', '<?php return [' . RepoPluginUuid::class . '::class => []];');

        if (is_file('/tmp/db.test.json')) {
            unlink('/tmp/db.test.json');
        }
        $table = $this->getRepo(Some::class, RepositoryFile::class, 'test');

        $this->assertInstanceOf(IRepository::class, $table);
        $this->assertEmpty($table->findAll());

        $item = $table->insertOne([
            'value' => 'some'
        ]);

        $this->assertInstanceOf(Some::class, $item);
        $this->assertEquals('some', $item['value']);
        $this->assertNotEmpty($item instanceof Some ? $item->getId() : '');
        $this->assertStringContainsString('-', $item instanceof Some ? $item->getId() : '');

        unlink('/tmp/db.test.json');
        unlink('/tmp/plugins.php');
    }
}
