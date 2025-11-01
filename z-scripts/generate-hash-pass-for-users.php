<?php
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$users = User::all();

foreach ($users as $user) {
    // اگر می‌خواهی همه پسوردها یکسان شود، مثلا '123'
    $user->password = '$2y$12$hJ/B2KFUdI.5LGZrSOzPt.yFCyxXWH2GNwEqgHNr4nkBnxYOjncNm'; // Bcrypt
    $user->save();
}

echo "All passwords updated to Bcrypt.\n";
