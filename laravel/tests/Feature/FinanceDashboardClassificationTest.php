<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\FinanceController;
use App\Models\Categorie;
use App\Models\Record;
use Carbon\CarbonImmutable;
use ReflectionMethod;
use Tests\TestCase;

class FinanceDashboardClassificationTest extends TestCase
{
    public function test_it_classifies_salary_and_website_as_income_types(): void
    {
        $this->assertSame('salary', $this->categoryType('SALAIRE', 'income'));
        $this->assertSame('website', $this->categoryType('NOM DE DOMAINE', 'daily_life'));
    }

    public function test_provision_is_not_treated_as_income_even_when_its_group_says_income(): void
    {
        $this->assertSame('provision', $this->categoryType('PROVISION', 'income'));
    }

    public function test_positive_transactions_in_expense_categories_remain_expense_adjustments(): void
    {
        $this->assertSame('expense', $this->categoryType('VACANCE', 'daily_life'));
    }

    public function test_salary_provision_website_and_category_refund_are_reported_separately(): void
    {
        $salary = $this->category(22, 'SALAIRE', 'income');
        $provision = $this->category(33, 'PROVISION', 'income');
        $website = $this->category(24, 'NOM DE DOMAINE', 'daily_life');
        $holiday = $this->category(47, 'VACANCE', 'daily_life');
        $transactions = collect([
            $this->transaction($salary, 2750, false),
            $this->transaction($provision, 1200, true),
            $this->transaction($holiday, 300, true),
            $this->transaction($holiday, 150, false),
            $this->transaction($website, 300, false),
        ]);
        $categoryTypes = collect([
            22 => 'salary',
            33 => 'provision',
            24 => 'website',
            47 => 'expense',
        ]);

        $method = new ReflectionMethod(FinanceController::class, 'summarizeDashboardTransactions');
        $summary = $method->invoke(new FinanceController, $transactions, $categoryTypes, collect([47]));

        $this->assertSame(3050.0, $summary['revenues']);
        $this->assertSame(2750.0, $summary['salary']);
        $this->assertSame(300.0, $summary['websiteRevenue']);
        $this->assertSame(1200.0, $summary['provisions']);
        $this->assertSame(150.0, $summary['expenses']);
        $this->assertSame(150.0, $summary['categories']->first()['refunds']);
    }

    public function test_mortgage_schedule_tracks_paid_and_remaining_installments(): void
    {
        $method = new ReflectionMethod(FinanceController::class, 'buildMortgageSchedule');
        $mortgage = $method->invoke(
            new FinanceController,
            265000.00,
            1125.28,
            0.00168,
            300,
            CarbonImmutable::parse('2018-12-30'),
            CarbonImmutable::parse('2026-11-13')
        );

        $this->assertCount(300, $mortgage['schedule']);
        $this->assertSame(95, $mortgage['paid_installments']);
        $this->assertSame(205, $mortgage['remaining_installments']);
        $this->assertSame('2018-12-30', $mortgage['schedule'][0]['date']->format('Y-m-d'));
        $this->assertSame('2019-02-28', $mortgage['schedule'][2]['date']->format('Y-m-d'));
        $this->assertSame('2026-11-30', $mortgage['next_payment_date']->format('Y-m-d'));
        $this->assertSame('2043-11-30', $mortgage['payoff_date']->format('Y-m-d'));
        $this->assertGreaterThan(0, $mortgage['remaining_principal']);
        $this->assertGreaterThan($mortgage['remaining_principal'], $mortgage['remaining_payments']);
        $this->assertSame(0.0, $mortgage['schedule'][299]['balance']);
    }

    private function categoryType(string $name, string $group): string
    {
        $method = new ReflectionMethod(FinanceController::class, 'dashboardCategoryType');

        return $method->invoke(new FinanceController, $this->category(1, $name, $group));
    }

    private function category(int $id, string $name, string $group): Categorie
    {
        $category = new Categorie;
        $category->id = $id;
        $category->nom = $name;
        $category->group = $group;
        $category->color = '#123456';

        return $category;
    }

    private function transaction(Categorie $category, float $amount, bool $isWithdrawal): Record
    {
        $transaction = new Record;
        $transaction->fk_id_categorie = $category->id;
        $transaction->montant = $amount;
        $transaction->retrait = $isWithdrawal;
        $transaction->date = '2024-01-15';
        $transaction->setRelation('category', $category);

        return $transaction;
    }
}
