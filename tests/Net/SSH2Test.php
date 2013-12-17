<?php

/**
 * @author     Marc Scholten <marc@pedigital.de>
 * @copyright  MMXIII Marc Scholten
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

class Net_SSH2Test extends PhpseclibTestCase
{
    /**
     * @return Net_SSH2
     */
    private function createSSHMock()
    {
        return $this->getMockBuilder('Net_SSH2')
            ->disableOriginalConstructor()
            ->setMethods(array('__destruct'))
            ->getMock();
    }
    
    public function formatLogDataProvider()
    {
        return array(
            array(
                array('hello world'),
                array('<--'),
                "<--\r\n00000000  68:65:6c:6c:6f:20:77:6f:72:6c:64                 hello world\r\n\r\n"
            ),
            array(
                array('hello', 'world'),
                array('<--', '<--'),
                "<--\r\n00000000  68:65:6c:6c:6f                                   hello\r\n\r\n" .
                "<--\r\n00000000  77:6f:72:6c:64                                   world\r\n\r\n"
            ),
        );
    }

    /**
     * @dataProvider formatLogDataProvider
     */
    public function testFormatLog(array $message_log, array $message_number_log, $expected)
    {
        $ssh = $this->createSSHMock();

        $result = $ssh->_format_log($message_log, $message_number_log);
        $this->assertEquals($expected, $result);
    }
    
    public function testGenerateIdentifierWithMcryptGmpAndBmath()
    {
        return array(
            array('SSH-2.0-phpseclib_0.3', array()),
            array('SSH-2.0-phpseclib_0.3 (gmp)', array('gmp')),
            array('SSH-2.0-phpseclib_0.3 (bcmath)', array('bcmath')),
            array('SSH-2.0-phpseclib_0.3 (mcrypt)', array('mcrypt')),
            array('SSH-2.0-phpseclib_0.3 (mcrypt, gmp)', array('mcrypt', 'gmp')),
            array('SSH-2.0-phpseclib_0.3 (mcrypt, bcmath)', array('mcrypt', 'bcmath')),
        );
    }

    /**
     * @dataProvider generateIdentifierProvider
     */
    public function testGenerateIdentifier($expected, array $requiredExtensions)
    {
        $notAllowed = array('gmp', 'bcmath', 'mcrypt', 'gmp');
        foreach($notAllowed as $nowAllowedExtension) {
            if(in_array($nowAllowedExtension, $requiredExtensions)) {
                continue;
            }

            if(extension_loaded($nowAllowedExtension)) {
                $this->markTestSkipped('Extension ' . $nowAllowedExtension . ' is not allowed for this data-set');
            }
        }

        $ssh = $this->createSSHMock();
        $identifier = $ssh->_generate_identifier();

        $this->assertEquals($expected, $identifier);
    }

}
