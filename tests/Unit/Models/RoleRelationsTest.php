<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use Tests\TestCase;

class RoleRelationsTest extends TestCase
{
    /**
     * Test if role with id=5 has assignedBy relationship
     */
    public function test_role_5_has_assigned_by_relationship(): void
    {
        // Try to find role with id=5
        $role = Role::find(5);
        
        if (!$role) {
            $this->markTestSkipped('Role with id=5 not found');
            return;
        }
        
        // Load assignedBy relationship
        $role->load('assignedBy');
        
        // Test that assignedBy relationship exists (can be empty collection)
        $this->assertNotNull($role->assignedBy);
        
        // Test it's a collection
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $role->assignedBy);
        
        echo "\nRole #5 assignedBy count: " . $role->assignedBy->count() . "\n";
        echo "Test completed successfully\n";
    }
}