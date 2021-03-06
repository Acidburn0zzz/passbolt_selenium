<?php

/**
 * Feature : Setup
 * As an anonymous user, I need to be able to see the setup page with an invitation to install the plugin.
 * As AP doing the setup, I should not be able to import a key already used by another user.
 * As an AP I should be able to download my private key after it is generated
 *
 * @TODO : Test a scenario where the key is not compatible with GPG on server side.
 * @TODO : Test scenario with a key that has matching information (same name and email).
 * @TODO : Test a scenario where the name of the user has to be altered.
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class SetupTest extends PassboltSetupTestCase {

	/**
	 * Scenario:  I can see the setup page with instructions to install the plugin
	 * Given      I am an anonymous user with no plugin on the registration page
	 * And        I follow the registration process and click on submit
	 * And        I click on the link get started in the email I received
	 * Then       Wait until I see the first page of setup.
	 * And        I should see the text "Nice one! The plugin is installed and up to date. You are good to go!"
	 * And        I should see that the domain in the url check textbox is the same as the one configured.
	 */
	public function testCanSeeSetupPageWithFirstPluginSection() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// We check below that we can read the invitation email and click on the link get started.
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Follow the link in the email.
		$this->followLink('get started');
		// Wait until I see the first page of setup.
		$this->waitForSection('domain_check');

		// Test that the plugin confirmation message is displayed.
		$this->waitUntilISee('.plugin-check.success', '/Nice one! The plugin is installed and up to date/i');

		// Test that the domain in the url check textbox is the same as the one configured.
		$domain = $this->findById('js_setup_domain')->getAttribute('value');
		$this->assertEquals(Config::read('passbolt.url'), $domain);

	}

	/**
	 * Scenario :   I go through the setup and I make sure the navigation buttons and menu items are working properly.
	 * Given        I am an anonymous user with the plugin on the first page of the setup
	 * Then         the menu "1. get the plugin" should be selected
	 * When         I check the domain validation checkbox
	 * And          I click on the link "Next"
	 * Then         I should see a page with a title "Create a new key"
	 * And          the menu "2. Define your keys" should be selected
	 * When         I click on the link "Cancel"
	 * Then         I should be back on the 1st step.
	 * When         I check the domain validation checkbox.
	 * And          I click "Next"
	 * When         I click "Import"
	 * Then         I should see a page where I can import my keys
	 * When         I click "Create"
	 * Then         I should be back on the page to generate a key
	 * When         I click "Next" again
	 * Then         I should be at the step 3
	 * And          I should see a page with title "Now let's setup your passphrase"
	 * And          The menu "3. Set a passphrase" should be selected
	 * When         I click "Cancel"
	 * Then         I should be back at step 2
	 * And          the menu "2. Define your keys should be selected"
	 * When         I click "Next"
	 * Then         I should be back at step 3
	 * When         I fill up a passphrase in the password field
	 * And          I click "Next"
	 * Then         I should reach a page saying that the secret and public key is generating
	 * And          I should wait until the key is generated
	 * And          I should reach the next step saying that the secret key is ready.
	 * And          I should see that the menu "3. Set a passphrase" is selected
	 * When         I click "Cancel"
	 * Then         I should be back at the step "enter passphrase"
	 * When         I enter the passphrase and click Next
	 * Then         I should see that the key generates again
	 * When         The key is generated and I reach the next step "Success! Your secret key is ready"
	 * And          I click "Next"
	 * Then         I should reach the next step
	 * And          I should see "Set a security token" as the title
	 * When         I click "Next"
	 * Then         I should reach the final step where I am being redirected
	 * And          The "Login !" menu should be selected
	 *
	 * @throws Exception
	 */
	public function testNavigation() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Go to Setup page.
		$this->goToSetup('johndoe@passbolt.com');
        // Wait until I see the first page of setup.
		$this->waitForSection('domain_check');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('domain_check', 'menu_item'));
		// Give it time to load the server key.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitForSection('generate_key_form');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_form', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Test that we are back at step 1.
		$this->waitForSection('domain_check');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('domain_check', 'menu_item'));
		// Wait the server key to be retrieved
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitForSection('generate_key_form');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->waitForSection('import_key_form');
		// Click on create.
		$this->clickLink('create');
		// Wait
		$this->waitForSection('generate_key_form');
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->waitForSection('generate_key_master_password');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_master_password', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait
		$this->waitUntilISee('#js_step_title', '/Create a new key/i');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_form', 'menu_item'));
		// Click Next.
		$this->clickLink("Next");
		// Wait until we see the title Master password.
		$this->waitForSection('generate_key_master_password');
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->waitUntilISee('#js_setup_submit_step.enabled');
		$this->clickLink("Next");
		// Wait to reach the page.
		// If the generation is too fast, the generate key progress cannot be tested.
		//$this->waitForSection('generate_key_progress');
		// Wait until the key is generated.
		$this->waitForSection('generate_key_done');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_done', 'menu_item'));
		// The key is generated, we can click Next.
		$this->clickLink("Cancel");
		// Wait until we see the title Master password.
		$this->waitForSection('generate_key_master_password');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_master_password', 'menu_item'));
		// Fill master key.
		$this->inputText('js_field_password', 'johndoemasterpassword');
		// Press Next.
		$this->waitUntilISee('#js_setup_submit_step.enabled');
		$this->clickLink("Next");
		// Wait to reach the page.
		// If the generation is too fast, the generate key progress cannot be tested.
		//$this->waitForSection('generate_key_progress');
		// Wait until we see the title Master password.
		$this->waitForSection('generate_key_done');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('generate_key_done', 'menu_item'));
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->waitForSection('security_token');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('security_token', 'menu_item'));
		// Test that Cancel button is working.
		$this->clickLink('Cancel');
		// Wait until we see the title Your secret key is ready.
		$this->waitForSection('generate_key_done');
		// Press Next.
		$this->clickLink("Next");
		// Wait.
		$this->waitForSection('security_token');
		// Press Next.
		$this->clickLink("Next");
		// Test that we are at the final step.
		$this->waitForSection('login_redirect');
		// Assert menu is selected.
		$this->assertMenuIsSelected($this->getSectionInfo('login_redirect', 'menu_item'));
	}

	/**
	 * @group saucelabs
	 * Scenario     As an AP using the setup, I should be able to go through all the steps of the setup
	 * Given        I am registered and on the first page of the setup
	 * Then         I should be able to verify the domain
	 * When         I click "Next"
	 * Then         I should be able to prepare the generation of my key
	 * When         I click "Next"
	 * Then         I should be able to enter a passphrase
	 * When         I click "Next"
	 * Then         The key should be generated and I should be able to download it
	 * When         I click "Next"
	 * Then         I should be able to choose a security token
	 * When         I click "Next"
	 * Then         I should be able to enter a password for my account
	 * When         I click "Next"
	 * Then         I should observe that I am logged in inside the app
	 * And          I should see my name and email in the account section
	 * @throws Exception
	 */
	public function testCanFollowSetupWithDefaultSteps() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		$john = User::get('john');
		// Register John Doe as a user.
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page and register
		$this->goToSetup($john['Username']);
		$this->completeRegistration();

		$this->loginAs([
			'Username' => $john['Username'],
			'MasterPassword' => $john['MasterPassword']
		]);
		// Check we are logged in.
		$this->waitCompletion();
		$this->waitUntilISee('#js_app_controller.ready');
		// Check that the name is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			$john['FirstName'] . ' ' . $john['LastName']
		);
		// Check that the email is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .email'),
			$john['Username']
		);
	}

	/**
	 * Scenario :  As an AP I should be able to import my own key during the setup
	 * Given       I am registered as John Doe, and I go to the setup
	 * When        I go through the setup until the import key step
	 * And         I test that I can import my key
	 * Then        I should see that the setup behaves as it should (defined in function testStepImportKey)
	 * When        I complete the setup
	 * Then        I should be logged in inside the app
	 * And         I should be able to visually confirm my account information
	 * @throws Exception
	 */
	public function testFollowSetupWithImportKey() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		$key = Gpgkey::get(['name' => 'johndoe']);

		$john = User::get('john');
		// Register John Doe as a user.
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page and register
		$this->goToSetup($john['Username']);
		// Wait
		$this->waitForSection('domain_check');
		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');
		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');
		// Click Next.
		$this->clickLink("Next");
		// Wait
		$this->waitForSection('generate_key_form');
		// Click on import.
		$this->clickLink('import');
		// Wait
		$this->waitForSection('import_key_form');
		// Test step import key.
		$this->completeStepImportKey($key);
		// Click Next
		$this->clickLink('Next');
		// Wait until next step.
		$this->waitForSection('security_token');
		// Click Next.
		$this->clickLink("Next");
		// Wait until sees next step.
		$this->waitForSection('login_redirect');
		// Wait until I reach the login page
		$this->waitUntilISee('.information h2', '/Welcome back!/');

		// Login as john doe
		$this->loginAs([
			'Username' => $key['owner_email'],
			'MasterPassword' => $key['masterpassword']
		]);

		$this->waitCompletion();
		// Check we are logged in.
		$this->waitUntilISee('.page.password', null, 20);
		// Check that the name is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .name'),
			$key['owner_name']
		);
		// Check that the email is ok.
		$this->assertElementContainsText(
			$this->findByCss('.header .user.profile .details .email'),
			$key['owner_email']
		);
	}

	/**
	 * @group no-saucelabs
	 *
	 * Scenario :  As an AP I should be able to download my private key after it is generated
	 * Given       I am registered as John Doe, and I go to the setup
	 * When        I go through the setup until the key backup step
	 * And         I click on download
	 * Then        I should see that the key downloaded is in a valid PGP format
	 * @throws Exception
	 */
	public function testSetupDownloadKeyAfterGenerate() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$john = User::get('john');
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page and register
		$this->goToSetup($john['Username']);

		// Test step domain verification.
		$this->completeStepDomainVerification();

		// Click Next.
		$this->clickLink("Next");
		// Test that button Next is disabled.
		$this->assertElementHasClass(
			$this->find('js_setup_submit_step'),
			'processing'
		);
		// test step that prepares key creation.
		$this->completeStepPrepareCreateKey($john);
		// Fill comment.
		$this->clickLink("Next");
		// Test enter passphrase step.
		$this->completeStepEnterMasterPassword($john);
		// Next.
		$this->clickLink("Next");
		// Generate key and wait for key backup screen.
		$this->completeStepGenerateAndDownloadKey();

		// Test that key has been downloaded.
		// Click on download option.
		$this->findById('js_backup_key_download')->click();
		sleep(2);

		// Go to the downloaded file url.
		$this->driver->get(Config::read('browsers.common.downloads_path') . DS . 'passbolt_private.asc.txt');

		// Get source code.
		$downloadedKey = $this->driver->getPageSource();

		// Assert that the key is in valid PGP format.
		$this->assertContains('BEGIN PGP PRIVATE KEY BLOCK', $downloadedKey);
		$this->assertContains('END PGP PRIVATE KEY BLOCK', $downloadedKey);
	}

	/**
	 * Scenario :   As an AP, I should not be able to do the setup after my account has been activated
	 * Given I click again on the link in the invitation email
	 * Then  I should not see the setup again
	 * And   I should see a page with a "Token not found" error
	 * @throws Exception
	 */
	public function testSetupNotAccessibleAfterAccountValidation() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$this->registerUser('John', 'Doe', 'johndoe@passbolt.com');

		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode('johndoe@passbolt.com'));
		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');

		// Go to setup page.
		$this->goToSetup('johndoe@passbolt.com');
		$this->completeRegistration();

		// Go to url remembered above.
		$this->driver->get($setupUrl);
		$this->waitUntilISee('h2', '/The authentication token is not valid/');
	}

	/**
	 * Scenario :   As an AP, I should be able to complete 2 setup consecutively.
	 * Given I have completed already one registration + setup successfully.
	 * When  I register again with a different username
	 * Then  I should be able to complete the setup another time without error.
	 * @throws Exception
	 */
	public function testSetupMultipleTimes() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$john = User::get('john');
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page.
		$this->goToSetup($john['Username']);
		$this->completeRegistration($john);

		// Register Curtis Mayfield as a user.
		$curtis = User::get('curtis');
		$this->registerUser($curtis['FirstName'], $curtis['LastName'], $curtis['Username']);

		// Go to setup page.
		$this->goToSetup($curtis['Username'], false);

		// Wait until I see the setup section domain check.
		$this->waitForSection('domain_check');

		// Complete registration.
		$this->completeRegistration($curtis);
	}


	/**
	 * Scenario :   As an AP, I should be able to restart the setup where I left it.
	 * Given I have completed already one registration and setup, but left the setup in the middle.
	 * When  I click again on the setup link in the email I received
	 * Then  I should see that the setup is restarting at the same screen where I was last time.
	 * When  I press Cancel
	 * Then  I should see that the setup is at the step before.
	 * @throws Exception
	 */
	public function testSetupRestartWhereItWasLeft() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$john = User::get('john');
		$this->registerUser($john['FirstName'] , $john['LastName'], $john['Username']);

		// Go to setup page.
		$this->goToSetup($john['Username']);

		// Test step domain verification.
		$this->completeStepDomainVerification();

		// Click Next.
		$this->clickLink("Next");

		// test step that prepares key creation.
		$this->completeStepPrepareCreateKey($john);

		// Fill comment.
		$this->clickLink("Next");

		// Go to setup page.
		// Get last email.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode($john['Username']));

		// Remember setup url. (We will use it later).
		$linkElement = $this->findLinkByText('get started');
		$setupUrl = $linkElement->getAttribute('href');

		// Go to url remembered above.
		$this->driver->get($setupUrl);

		// Wait until passphrase section appears.
		$this->waitForSection('generate_key_master_password');

		// Test that Cancel button is working.
		$this->clickLink('Cancel');

		// I should see the previous section generate_key_form.
		$this->waitForSection('generate_key_form');
	}

	/**
	 * Scenario :   As an AP trying to complete the setup a second time, I should see a warning informing me that the plugin is already configured.
	 * Given I have completed already one registration + setup successfully (without seeing a warning)
	 * When  I register again with a different username
	 * And   I begin the setup process
	 * Then  I should see a warning informing me that the plugin is already configured.
	 * @throws Exception
	 */
	public function testSetupDisplayWarningIfAlreadyConfigured() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Register John Doe as a user.
		$john = User::get('john');
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page.
		$this->goToSetup($john['Username']);

		// Wait until I see the setup section domain check.
		$this->waitForSection('domain_check');

		// I should not se any warning.
		$this->assertNotVisible('.plugin-check.warning');

		// Complete registration.
		$this->completeRegistration($john);

		// Register Curtis Mayfield as a user.
		$curtis = User::get('curtis');
		$this->registerUser($curtis['FirstName'], $curtis['LastName'], $curtis['Username']);

		// Go to setup page.
		$this->goToSetup($curtis['Username'], false);

		// Wait until I see the setup section domain check.
		$this->waitForSection('domain_check');

		$this->waitUntilISee('.plugin-check.warning');
		$this->assertElementContainsText(
			$this->find('.plugin-check.warning'),
			'The plugin is already configured'
		);
	}

	/**
	 * Scenario:    As AP doing the setup, I should not be able to import a key already used by another user.
	 * Given    I have registered and I am following the setup
	 * When     I am at the import step, and I try to import a key that is already in use by another user (example: Ada).
	 * Then     I should see an error message informing me that this key is already in use.
	 *
	 * @throws Exception
	 */
	public function testFollowSetupWithImportNonUniqueKey() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		$john = User::get('john');
		$john['PrivateKey'] = 'ada_private_nopassphrase.key';

		// Register John Doe as a user.
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page and register
		$this->goToSetup($john['Username']);

		// Wait
		$this->waitForSection('domain_check');

		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');

		// Click Next.
		$this->clickLink("Next");

		// Wait
		$this->waitForSection('generate_key_form');

		// Click on import.
		$this->clickLink('import');

		// Wait
		$this->waitForSection('import_key_form');

		// Insert Ada's key instead of John's key (Ada's key already exist in database).
		$keyData = file_get_contents(GPG_FIXTURES . DS .  $john['PrivateKey']);
		$this->inputText('js_setup_import_key_text', $keyData);

		// Click Next
		$this->clickLink('Next');

		// I should see an error message.
		$this->waitUntilISee('KeyErrorMessage', '/This key is already used by another user/');
	}

	/**
	 * Scenario:    As AP doing the setup, I should be able to import a key already used by another user who is soft deleted.
	 * Given    I first login as admin and I delete Ada
	 * When     I have registered as a new user and I am following the setup
	 * When     I am at the import step, and I try to import a key that was already used by a deleted user.
	 * Then     I should see that the key is imported normally.
	 *
	 * @throws Exception
	 */
	public function testFollowSetupWithImportNonUniqueKeyBelongingToDeletedUser() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// And I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$userU = User::get('ursula');
		$this->rightClickUser($userU['id']);

		// Then I select the delete option in the contextual menu
		$this->click('#js_user_browser_menu_delete a');

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete user ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Then I should see a success notification message saying the user is deleted
		$this->assertNotification('app_users_delete_success');

		$this->logout();

		$john = User::get('john');
		$john['PrivateKey'] = 'ursula_private.key';

		// Register John Doe as a user.
		$this->registerUser($john['FirstName'], $john['LastName'], $john['Username']);

		// Go to setup page and register
		$this->goToSetup($john['Username'], false);

		// Wait
		$this->waitForSection('domain_check');

		// Wait for the server key to be retrieved.
		$this->waitUntilISee('.why-plugin-wrapper', '/I\'ve checked/i');

		// Check box domain check.
		$this->checkCheckbox('js_setup_domain_check');

		// Click Next.
		$this->clickLink("Next");

		// Wait
		$this->waitForSection('generate_key_form');

		// Click on import.
		$this->clickLink('import');

		// Wait
		$this->waitForSection('import_key_form');

		// Insert Ada's key instead of John's key (Ada's key already exist in database).
		$keyData = file_get_contents(GPG_FIXTURES . DS .  $john['PrivateKey']);
		$this->inputText('js_setup_import_key_text', $keyData);

		// Click Next
		$this->clickLink('Next');

		// Wait until section appears.
		$this->waitForSection('import_key_done');

		// I should see a success message.
		$this->assertElementContainsText(
			$this->findByCss('.message.warning'),
			'Warning'
		);
	}
}