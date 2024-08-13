<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\User;
use App\Services\BankAccountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class BankAccountServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var BankAccountService
     */
    protected BankAccountService $bankAccountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bankAccountService = new BankAccountService();
    }

    public function test_it_can_create_a_bank_account_as_a_user()
    {
        $user = User::factory()->create();

        $bank_account = $this->bankAccountService->createAccount($user, 'Rome');

        $this->assertEquals($bank_account->owner_id, $user->id);

        $this->assertDatabaseHas('bank_accounts', [
            'balance' => 0,
            'owner_id' => $user->id,
            'company_id' => null
        ]);
    }

    public function test_it_can_create_only_one_bank_account_as_a_user()
    {
        $user = User::factory()->create();

        (new BankAccountService())->createAccount($user, 'Washington DC');

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized action.');

        $this->bankAccountService->createAccount($user, 'Florida');
    }

    public function test_it_can_create_a_bank_account_for_a_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);

        $bank_account = $this->bankAccountService->createAccount($user, 'Panama',$company->id);

        $this->assertEquals($bank_account->company_id, $company->id);

        $this->assertDatabaseHas('bank_accounts', [
            'balance' => 0,
            'owner_id' => null,
            'company_id' => $company->id
        ]);
    }

    public function test_it_can_create_only_one_bank_account_for_a_company()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['owner_id' => $user->id]);
        $this->bankAccountService->createAccount($user, 'Medellin', $company->id);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Unauthorized action.');

        $this->bankAccountService->createAccount($user, 'Bogota', $company->id);
    }
}
