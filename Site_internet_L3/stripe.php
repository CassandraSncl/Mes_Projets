<?php
require_once 'vendor/autoload.php';

// Clés d'API Stripe
$stripe_secret_key = 'sk_test_51OFx3PCmz9ktjvlGYGermIUCtGlIQZGO6hTS5qWIUrDKpU9DL9uRuR908p3kNsqHBJIc4kmmwpsen8OzkIajiJyM00KPRd7ND7';
$stripe_public_key = 'pk_test_51OFx3PCmz9ktjvlGHMYpCZqahpUJWQ9jUIxiOBVUlXfitWUA5VgbxXCYpLufzJRLCfiXi1zCs7XJ9JbgJx9QmCWM00dioePmYk';

// Objet Stripe
$stripe = new \Stripe\StripeClient($stripe_secret_key);
?>