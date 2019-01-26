<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Master\DeviceType;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class BlockControllerTest extends AbstractAdminWebTestCase
{
    public function test_routing_AdminContentBlock_index()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_block'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentBlock_edit()
    {
        $this->client->request('GET',
            $this->generateUrl(
                'admin_content_block_edit',
                ['id' => 1]
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentBlock_editWithPost()
    {
        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_block_edit', ['id' => 1]),
            [
                'block' => [
                    'name' => 'newblock',
                    'file_name' => 'file_name',
                    'block_html' => '<p>test</p>',
                    'DeviceType' => DeviceType::DEVICE_TYPE_MB,
                    'id' => 1,
                    '_token' => 'dummy',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_content_block_edit', ['id' => 1])
        ));

        $dir = sprintf('%s/app/template/%s/Block',
            $this->container->getParameter('kernel.project_dir'),
            $this->container->getParameter('eccube.theme'));

        $this->expected = '<p>test</p>';
        $this->actual = file_get_contents($dir.'/file_name.twig');
        $this->verify();

        // Filesystem::dumpFile() がエラーになるので bovigo\vfs が使用できない
        if (file_exists($dir.'/file_name.twig')) {
            unlink($dir.'/file_name.twig');
        }
    }

    public function test_routing_AdminContentBlock_defaultBlockDelete()
    {
        $this->loginTo($this->createMember());

        $redirectUrl = $this->generateUrl('admin_content_block');

        $this->client->request('DELETE',
            $this->generateUrl('admin_content_block_delete', ['id' => 1])
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }
}