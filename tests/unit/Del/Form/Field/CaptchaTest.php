<?php

namespace DelTesting\Form\Field;

use Codeception\TestCase\Test;
use DateTime;
use Del\Form\Field\Captcha;
use Del\Form\Field\Captcha\FigletCaptcha;
use Del\Form\Form;
use Del\SessionManager;

class CaptchaTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Form
     */
    protected $form;

    protected function _before()
    {
        $this->form = new Form('captchaform');
    }

    protected function _after()
    {
        unset($this->form);
    }

    /**
     * @throws \Exception
     */
    public function testFiglet()
    {
        $session = SessionManager::getInstance();
        $captcha = new Captcha('captcha');
        $captcha->setRequired(true);
        $figlet = new FigletCaptcha($session);
        $captcha->setCaptchaAdapter($figlet);
        $this->form->addField($captcha);
        $session = SessionManager::get('captcha');
        $session['expires'] = new DateTime('@' . (time() +10));
        SessionManager::set('session', $session);
        $figlet->generate();
        $this->assertFalse($captcha->isValid());
        $html = $figlet->render();
        $this->assertTrue((bool) preg_match('#<div class="mono">.+\n.+\n.+\n.+\n.+\n.+\n.+\n<\/div>#', $html));
    }
}
