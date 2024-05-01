<?php

namespace AlbumTest\Model;

use Album\Model\Album;
use Album\Model\AlbumTable;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\TableGateway\TableGatewayInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AlbumTableTest extends TestCase
{
    private $tableGateway;
    private $albumTable;

    protected function setUp(): void
    {
        $this->tableGateway = $this->createMock(TableGatewayInterface::class);
        $this->albumTable = new AlbumTable($this->tableGateway);
    }

    public function testFetchAllReturnsAllAlbums(): void
    {
        $resultSet = $this->createMock(ResultSetInterface::class);
        $this->tableGateway->expects($this->once())
            ->method('select')
            ->willReturn($resultSet);

        $this->assertSame($resultSet, $this->albumTable->fetchAll());
    }

    public function testCanDeleteAnAlbumByItsId(): void
    {
        $this->tableGateway->expects($this->once())
            ->method('delete');
        
        $this->albumTable->deleteAlbum(123);
    }

    public function testSaveAlbumWillInsertNewAlbumsIfTheyDontAlreadyHaveAnId(): void
    {
        $albumData = [
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams'
        ];

        $album = new Album();
        $album->exchangeArray($albumData);

        $this->tableGateway->expects($this->once())
            ->method('insert');

        $this->albumTable->saveAlbum($album);
    }

    public function testSaveAlbumWillUpdateExistingAlbumsIfTheyAlreadyHaveAnId(): void
    {
        $albumData = [
            'id'     => 123,
            'artist' => 'The Military Wives',
            'title'  => 'In My Dreams',
        ];
        
        $album = new Album();
        $album->exchangeArray($albumData);

        $resultSet = $this->createMock(ResultSetInterface::class);
        $resultSet->expects($this->once())
            ->method('current')
            ->willReturn($album);

        $this->tableGateway->expects($this->once())
            ->method('select')
            ->with(['id' => 123])
            ->willReturn($resultSet);
            
        $this->tableGateway->expects($this->once())
            ->method('update')
            ->with(
                array_filter($albumData, function ($key) {
                    return in_array($key, ['artist', 'title']);
                }, ARRAY_FILTER_USE_KEY),
                ['id' => 123]
            );

        $this->albumTable->saveAlbum($album);
    }

    public function testExceptionIsThrownWhenGettingNonExistentAlbum(): void
    {
        $resultSet = $this->createMock(ResultSetInterface::class);
        $resultSet->expects($this->once())
            ->method('current')
            ->willReturn(null);

        $this->tableGateway->expects($this->once())
            ->method('select')
            ->willReturn($resultSet);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find row with identifier 123');
        $this->albumTable->getAlbum(123);
    }
}
