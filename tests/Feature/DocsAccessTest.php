<?php

namespace Tests\Feature;

use Tests\TestCase;

class DocsAccessTest extends TestCase
{
    public function test_docs_page_is_public(): void
    {
        $this->get('/docs')->assertOk();
    }

    public function test_openapi_spec_is_public(): void
    {
        // Public route: must not be blocked by the docs.auth middleware.
        config(['docs.token' => 'test-token']);

        $this->get('/api/openapi.json')->assertStatus(200);
    }

    public function test_protected_endpoint_rejects_a_wrong_token(): void
    {
        config(['docs.token' => 'test-token']);

        $this->withToken('wrong-token')->getJson('/api/suppliers')->assertStatus(401);
    }
}
