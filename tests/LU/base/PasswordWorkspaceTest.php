<?php

/**
 * Feature : Password Workspace
 *
 * - As a user I should be able to see the passwords workspace
 * - As a user I should be able to browse my passwords
 * - As a user I should be able to use the navigation filters
 * - As a user I should be able to view my password details
 * - As a user I should be able to fav/unfav
 * - As a user I should be able to search by keywords
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence            GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PasswordWorkspaceTest extends PassboltTestCase
{

	protected function setUp()
	{
		parent::setUp();
		// Reset passbolt installation with dummies.
		//$this->PassboltServer->resetDatabase(1);
	}

	/**
	 * Scenario :   As a user I should be able to see the passwords workspace
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * Then			I should see the workspace primary menu
	 * And			I should see the workspace secondary menu
	 * And 			I should see the workspace filters shortcuts
	 * And          I should see a grid and its columns
	 */
	public function testWorkspace()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I should the workspace primary menu.
		$buttons = ['create', 'edit', 'delete', 'share', 'more'];
		for ($i = 0; $i < count($buttons); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_primary_menu_wrapper ul'),
				$buttons[$i]
			);
		}

		// I should the workspace filters.
		$filters = ['All items', 'Favorite', 'Recently modified', 'Shared with me', 'Items I own'];
		for ($i = 0; $i < count($filters); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_rs_shortcuts'),
				$filters[$i]
			);
		}

		// I should see a grid and its columns
		$columns = ['Resource', 'Username', 'Password', 'URI', 'Modified'];
		for ($i = 0; $i < count($columns); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-header'),
				$columns[$i]
			);
		}

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to see my passwords
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * Then         I should see rows representing my passwords
	 */
	public function testBrowsePasswords()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I should see rows representing my passwords
		$passwords = ['shared resource', 'salesforce account', 'tetris license', 'facebook account'];
		for ($i = 0; $i < count($passwords); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
				$passwords[$i]
			);
		}

		// @todo Test de rows details

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to filter my passwords
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on the favorite filter
	 * Then			I should only see my favorite passwords
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Favorite
	 * When			I click on the recently modified filter
	 * Then			I should see my passwords ordered my modification date
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Recently modified
	 * When			I click on the shared with me filter
	 * Then			I should only see the passwords that have been share with me
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Shared with me
	 * When			I click on the items I own filter
	 * Then			I should only see the passwords I own
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Items I own
	 */
	public function testFilterPasswords()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on the favorite filter
		$this->clickLink("Favorite");
		$this->waitCompletion();
		// I should only see my favorite passwords
		// @todo Test with a case which already has favorites
		// I should see the breadcrumb with the following:
		// 	| All items
		//	| Search : Favorite
		$this->assertBreadcrumb(['All items', 'Favorite']);

		// I click on the recently modified filter
		$this->clickLink("Recently modified");
		$this->waitCompletion();
		// I should see my passwords ordered my modification date
		// @todo Test with a case where the modified date are different
		// I should see the breadcrumb with the following:
		// 	| All items
		//	| Search : Favorite
		$this->assertBreadcrumb(['All items', 'Recently modified']);

		// I click on the shared with me filter
		$this->clickLink("Shared with me");
		$this->waitCompletion();
		// I should only see the passwords that have been share with me
		$passwords = ['shared resource', 'salesforce account', 'tetris license', 'facebook account'];
		for ($i = 0; $i < count($passwords); $i++) {
			$this->assertElementContainsText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
				$passwords[$i]
			);
		}
		// I should see the breadcrumb with the following:
		// 	| All items
		//	| Search : Shared with me
		$this->assertBreadcrumb(['All items', 'Shared with me']);

		// I click on the items I own filter
		$this->clickLink("Items I own");
		$this->waitCompletion();
		// I should only see the passwords I own
		// @todo Test with a case which owns some passwords
		// I should see the breadcrumb with the following:
		// 	| All items
		//	| Search : Shared with me
		$this->assertBreadcrumb(['All items', 'Items I own']);

		$this->assertTrue(true);
	}

	/**
	 * Scenario :   As a user I should be able to view my password details
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on a password
	 * Then 		I should see a secondary side bar appearing
	 * And			I should the details of the selected password
	 */
	public function testPasswordDetails()
	{
		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on a password
		$this->clickElement("#js_wsp_pwd_browser .tableview-content div[title='shared resource']");
		$this->waitCompletion();

		// I should see a secondary side bar appearing
		$this->assertPageContainsElement('#js_pwd_details');

		// I should the details of the selected password
		$pwdDetails = [
			'username' 		=> 'admin',
			'url' 			=> 'http://shared.resource.net/',
			'modified' 		=> '2 years ago',
			'created-by' 	=> 'anonymous@passbolt.com',
			'modified-by' 	=> 'anonymous@passbolt.com',
		];
		// I should see the password's username
		$cssSelector = '#js_pwd_details .detailed-information li.username';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['username']
		);
		// I should see the password's url
		$cssSelector = '#js_pwd_details .detailed-information li.url';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['url']
		);
		// I should see the password's modified time
		$cssSelector = '#js_pwd_details .detailed-information li.modified';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified']
		);
		// I should see the password's creator
		$cssSelector = '#js_pwd_details .detailed-information li.created-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['created-by']
		);
		// I should see the password's modifier
		$cssSelector = '#js_pwd_details .detailed-information li.modified-by';
		$this->assertElementContainsText(
			$this->findByCss($cssSelector),
			$pwdDetails['modified-by']
		);
	}

	/**
	 * Scenario :   As a user I should be able to fav/unfav
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I click on the favorite star located before the password (the password shouldn't be a favorite)
	 * Then 		I should see the star becoming red
	 * And			I should see a confirmation of my action in the notification area
	 * When 		I click on the favorite filter
	 * Then			I should see the password I just added to my favorites in the list of passwords
	 * When			I click on the favorite red star located before the password (the password has to be a favorite)
	 * Then			I should see the star becoming white
	 * And			I should see a confirmation of my action in the notification area
	 * When 		I click on the favorite filter
	 * Then			I shouldn't see anymore the password in my list of favorite passwords
	 */
	public function testFavorite()
	{
		$passwordTitle = 'tetris license';
		$xpathFavSelector = "//tr[*/div[contains(.,'" . $passwordTitle . "')]]//i[contains(@class, fav)]";
		$xpathUnfavSelector = "//tr[*/div[contains(.,'" . $passwordTitle . "')]]//i[contains(@class, unfav)]";

		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I click on the favorite star located before the password (the password shouldn't be a favorite)
		$favElt = $this->findByXpath($xpathFavSelector);
		$favElt->click();
		$this->waitCompletion();

		// I should see the star becoming red
		// The following operation should throw an exception if the element is not found
		$unfavElt = $this->findByXpath($xpathUnfavSelector);

		// I should see a confirmation of my action in the notification area
		$this->assertPageContainsText('This record was successfully starred!');

		// I click on the favorite filter
		$this->clickLink("Favorite");
		$this->waitCompletion();

		// I should see the password I just added to my favorites in the list of passwords
		$this->assertElementContainsText(
			$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
			$passwordTitle
		);

		// I click on the favorite red star located before the password (the password has to be a favorite)
		$unfavElt = $this->findByXpath($xpathUnfavSelector);
		$unfavElt->click();
		$this->waitCompletion();

		// I should see the star becoming white
		// The following operation should throw an exception if the element is not found
		$favElt = $this->findByXpath($xpathFavSelector);

		// I should see a confirmation of my action in the notification area
		// @todo Cannot perform this assert because of the time we want a message to stay in the notification area.
		// $this->assertPageContainsText('This record was removed from your starred item list');

		// I click on the favorite filter
		$this->clickLink("Favorite");
		$this->waitCompletion();

		// I shouldn't see anymore the password in my list of favorite passwords
		$this->assertElementNotContainText(
			$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
			$passwordTitle
		);
	}

	/**
	 * Scenario :   As a user I should be able to search by keywords
	 * Given        I am logged in as Cedric Alfonsi, and I go to the password workspace
	 * When			I fill the "app search" field with "tetris license"
	 * And			I click "search"
	 * Then 		I should see the view filtered with my search
	 * And			I should see the breadcrumb with the following:
	 *					| All items
	 *					| Search : tetris license
	 */
	public function testSearchByKeywords()
	{
		$searchPwd = 'tetris license';
		$hiddenPwd = ['shared resource', 'salesforce account', 'facebook account'];
		$breadcrumb = ['All items', 'Search : tetris license'];

		// I am logged in as Cedric Alfonsi, and I go to the password workspace
		$this->getUrl('login');
		$this->inputText('UserUsername', 'cedric@passbolt.com');
		$this->inputText('UserPassword', 'password');
		$this->pressEnter();

		// I wait the current operation has been completed.
		$this->waitCompletion();

		// I fill the "app search" field with "tetris license"
		$this->inputText('js_app_filter_keywords', $searchPwd);
		$this->clickElement("#js_app_filter_form button[value='search']");
		$this->waitCompletion();

		// I should see the view filtered with my search
		$this->assertElementContainsText(
			$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
			$searchPwd
		);
		for ($i=0; $i< count($hiddenPwd); $i++) {
			$this->assertElementNotContainText(
				$this->findByCss('#js_wsp_pwd_browser .tableview-content'),
				$hiddenPwd[$i]
			);
		}

		// I should see the breadcrumb with the following:
		// 	| All items
		//	| Search : tetris license
		$this->assertBreadcrumb($breadcrumb);
	}

}