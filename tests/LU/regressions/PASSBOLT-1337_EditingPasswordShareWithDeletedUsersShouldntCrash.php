<?php
/**
 * Bug PASSBOLT-1337 - Regression test
 *
 * @copyright (c) 2017 Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PASSBOLT1337 extends PassboltTestCase {

	/**
	 * Scenario: As a user while editing a password that had been shared with a deleted user, the application shouldn't crash silently
	 *
	 * Given        I am logged in as admin in the user workspace
	 * And          I click on the user
	 * And          I click on delete button
	 * Then         I should see a confirmation dialog
	 * When         I click ok in the confirmation dialog
	 * Then         I should see a confirmation message
	 *
	 * When     	I logout and I log in as Ada
	 * And      	I go on the password workspace
	 * And      	I am editing a password that was shared with betty
	 * When     	I click on name input text field
	 * And      	I empty the name input text field value
	 * And      	I enter a new value
	 * And      	I click save
	 * Then     	I can see a success notification
	 * And      	I can see that the password name have changed in the overview
	 * And      	I can see the new name value in the sidebar
	 * When     	I click edit button
	 * Then     	I can see the new name in the edit password dialog
	 */
	public function testEditingPasswordSharedWithDeletedUsersShouldntCrash() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Admin
		$user = User::get('admin');
		$this->setClientConfig($user);

		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('user');

		// When I right click on a user
		$user = User::get('betty');
		$this->clickUser($user['id']);

		// Then I select the delete option in the contextual menu
		$this->click('js_user_wk_menu_deletion_button');

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete user ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Then I should see a success notification message saying the user is deleted
		$this->assertNotification('app_users_delete_success');


		// When I logout
		$this->logout();

		// And I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// And I am editing a password that was shared with betty
		$this->gotoEditPassword(Uuid::get('resource.id.apache'));

		// When I click on name input text field
		$this->click('js_field_name');

		// And I empty the name input text field value
		// And I enter a new value
		$newname = 'New password name';
		$this->inputText('js_field_name',$newname);

		// And I click save
		$this->click('.edit-password-dialog input[type=submit]');

		// Then I can see a success notification
		$this->assertNotification('app_resources_edit_success');

		// And I can see that the password name have changed in the overview
		$this->assertElementContainsText('#js_wsp_pwd_browser .tableview-content', $newname);

		// And I can see the new name value in the sidebar
		$this->assertVisible('#js_pwd_details.panel.aside');
		$this->assertElementContainsText('js_pwd_details', $newname);

		// When I click edit button
		$this->click('js_wk_menu_edition_button');

		// Then I can see the new name in the edit password dialog
		$this->assertInputValue('js_field_name', $newname);
	}
}