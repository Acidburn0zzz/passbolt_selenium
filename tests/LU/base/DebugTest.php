<?php

class DebugTest extends PassboltTestCase {
	public function testSetDebug() {
		$this->getUrl();
		$this->assertCurrentRole('guest');
		$this->assertPlugin();
		$this->assertNoPluginConfig();

		$this->getUrl('debug');
		try {
			$this->findByCss('.page.debug.plugin');
		} catch (NoSuchElementException $e) {
			$this->fail('Page debug config not found');
		}

		$this->inputText('ProfileFirstName','Remy');
		$this->inputText('ProfileLastName','Bertot');
		$this->inputText('UserUsername','remy@passbolt.com');

		$this->inputText('securityTokenCode','RCB');
		$this->inputText('securityTokenColor','#ff3a3a');
		$this->inputText('securityTokenTextColor','#ffffff');

		$this->click('js_save_conf');

		$key = '-----BEGIN PGP PRIVATE KEY BLOCK-----
Version: GnuPG/MacGPG2 v2.0.22 (Darwin)
Comment: GPGTools - https://gpgtools.org

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
lvnMk+ASelh2uLAKBwV11m/7NPUtXGIKikrGag==
=9zwB
-----END PGP PRIVATE KEY BLOCK-----';

		$this->inputText('keyAscii',$key);
		$this->click('saveKey');

		$this->getUrl('login');
		$this->inputText('UserUsername','remy@passbolt.com');
		$this->inputText('UserPassword','password');
		$this->pressEnter();

	}
}