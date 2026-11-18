<?php

test('deploy-run rejects unauthorized requests without key', function () {
    $response = $this->get('/deploy-run');
    $response->assertStatus(403);
});

test('deploy-run rejects requests with invalid key', function () {
    config(['app.deploy_secret' => 'valid-secret-key-123']);

    $response = $this->get('/deploy-run?key=invalid-key');
    $response->assertStatus(403);
});

test('deploy-run succeeds with valid key', function () {
    config(['app.deploy_secret' => 'valid-secret-key-123']);

    $response = $this->get('/deploy-run?key=valid-secret-key-123');
    $response->assertOk()
        ->assertJson([
            'status' => 'success',
        ]);
});
