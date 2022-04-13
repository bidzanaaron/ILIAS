<?php declare(strict_types=1);

/* Copyright (c) 1998-2021 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class ilMailOptionsTest
 * @author Niels Theen <ntheen@databay.de>
 * @author Michael Jansen <mjansen@databay.de>
 */
class ilMailOptionsTest extends ilMailBaseTest
{
    /**
     * @throws ReflectionException
     */
    public function testConstructor() : void
    {
        $userId = 1;

        $database = $this->getMockBuilder(ilDBInterface::class)
            ->getMock();
        $queryMock = $this->getMockBuilder(ilDBStatement::class)
            ->getMock();

        $object = new stdClass();
        $object->cronjob_notification = false;
        $object->signature = 'smth';
        $object->linebreak = 0;
        $object->incoming_type = 1;
        $object->mail_address_option = 0;
        $object->email = 'test@test.com';
        $object->second_email = 'ilias@ilias.com';

        $database->expects($this->once())->method('fetchObject')->willReturn($object);
        $database->expects($this->once())->method('queryF')->willReturn($queryMock);

        $this->setGlobalVariable('ilDB', $database);

        $settings = $this->getMockBuilder(ilSetting::class)->disableOriginalConstructor()->onlyMethods([
            'set',
            'get',
        ])->getMock();
        $this->setGlobalVariable('ilSetting', $settings);

        $mailOptions = new ilMailOptions($userId);
        $this->assertSame($object->signature, $mailOptions->getSignature());
        $this->assertSame($object->incoming_type, $mailOptions->getIncomingType());
        $this->assertSame($object->linebreak, $mailOptions->getLinebreak());
        $this->assertSame($object->cronjob_notification, $mailOptions->isCronJobNotificationEnabled());
    }
}
