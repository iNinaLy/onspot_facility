<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Firestore;

class FirebaseService
{
    protected $firebase;
    protected $auth;
    protected $database;
    protected $firestore;

    public function __construct()
    {
        // Initialize Firebase using credentials from the environment file
        $this->firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));

        // Create instances of Firebase services
        $this->auth = $this->firebase->createAuth();
        $this->database = $this->firebase->createDatabase();
        $this->firestore = $this->firebase->createFirestore();
    }

    /**
     * Create a new user in Firebase Authentication
     *
     * @param string $email
     * @param string $password
     * @return \Kreait\Firebase\Auth\UserRecord
     */
    public function createUser($email, $password)
    {
        $userProperties = [
            'email' => $email,
            'password' => $password,
        ];

        return $this->auth->createUser($userProperties);
    }

    /**
     * Get a reference from Firebase Realtime Database
     *
     * @param string $reference
     * @return \Kreait\Firebase\Database\Reference
     */
    public function getDatabaseReference($reference)
    {
        return $this->database->getReference($reference);
    }

    /**
     * Add a document to Firestore
     *
     * @param string $collection
     * @param array $data
     * @return \Kreait\Firebase\Firestore\DocumentReference
     */
    public function addFirestoreDocument($collection, $data)
    {
        $collectionReference = $this->firestore->database()->collection($collection);
        return $collectionReference->add($data);
    }
}