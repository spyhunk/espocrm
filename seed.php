<?php

include "bootstrap.php";

use Espo\Core\Application;
use Espo\ORM\EntityManager;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Initializing Application...\n";

try {
    $app = new Application();
    
    if (!$app->isInstalled()) {
        die("Error: EspoCRM is not installed.\n");
    }

    $app->setupSystemUser();
    $container = $app->getContainer();

    /** @var EntityManager $entityManager */
    $entityManager = $container->get('entityManager');

    echo "Starting seed data generation...\n";


// 1. Create Lead with email
$leadEmail = 'test-lead@example.com';
$lead = $entityManager->getEntity('Lead');
$lead->set([
    'name' => 'Test Lead for Button',
    'emailAddress' => $leadEmail,
    'description' => 'Lead to test "Find contacts" button.'
]);
$entityManager->saveEntity($lead);
echo "Created Lead: " . $lead->get('name') . " (ID: " . $lead->getId() . ")\n";

// 2. Create Contacts for Lead Button Test
$contact1 = $entityManager->getEntity('Contact');
$contact1->set([
    'firstName' => 'Alice',
    'lastName' => 'Matcher',
    'emailAddress' => $leadEmail,
    'title' => 'CEO'
]);
$entityManager->saveEntity($contact1);
echo "Created Contact: " . $contact1->get('name') . " (Matches Lead Email)\n";

$contact2 = $entityManager->getEntity('Contact');
$contact2->set([
    'firstName' => 'Bob',
    'lastName' => 'Matcher',
    'emailAddress' => $leadEmail,
    'title' => 'CTO'
]);
$entityManager->saveEntity($contact2);
echo "Created Contact: " . $contact2->get('name') . " (Matches Lead Email)\n";

// 3. Create Contacts for Filter Test
$contactWithPhone = $entityManager->getEntity('Contact');
$contactWithPhone->set([
    'firstName' => 'Charlie',
    'lastName' => 'HasPhone',
    'phoneNumber' => '555-0100',
    'emailAddress' => 'charlie@example.com'
]);
$entityManager->saveEntity($contactWithPhone);
echo "Created Contact: " . $contactWithPhone->get('name') . " (Has Phone)\n";

$contactNoPhone = $entityManager->getEntity('Contact');
$contactNoPhone->set([
    'firstName' => 'Dave',
    'lastName' => 'NoPhone',
    'emailAddress' => 'dave@example.com'
]);
$entityManager->saveEntity($contactNoPhone);
echo "Created Contact: " . $contactNoPhone->get('name') . " (No Phone)\n";

// 4. Create Account and Link Contacts for Hook Test
$account = $entityManager->getEntity('Account');
$account->set([
    'name' => 'Test Account for Hook',
    'billingAddressCity' => 'Test City'
]);
$entityManager->saveEntity($account);
echo "Created Account: " . $account->get('name') . " (ID: " . $account->getId() . ")\n";

// Link contacts to account
// Note: We need to refresh account or fetch it again to ensure relations work if we were using getLink, 
// but here we are just setting the relation.
// Actually, to trigger the hook on Account save, we should link first then save Account again?
// The hook is on Account beforeSave. It reads related contacts.
// So we need to link contacts to the account, and THEN save the account to trigger the hook logic 
// (or rely on the fact that linking might not trigger account save depending on implementation, 
// but usually we want to test the hook when we edit the account).
// Let's link them first.

$entityManager->getRepository('Account')->getRelation($account, 'contacts')->relate($contact1);
$entityManager->getRepository('Account')->getRelation($account, 'contacts')->relate($contactWithPhone);

echo "Linked contacts to Account.\n";

// Now save Account again to trigger the hook (simulating a user edit)
// We need to fetch it fresh or just save it.
$account->set('name', 'Test Account for Hook (Updated)');
$entityManager->saveEntity($account);

echo "Updated Account to trigger hook.\n";
echo "Check Account Description: \n" . $account->get('description') . "\n";

    echo "Done.\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
