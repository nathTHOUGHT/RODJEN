<?php

beforeEach(function () {
    config()->set('preview.enabled', true);

    $this->withSession([
        'preview.user' => [
            'username' => 'admin',
            'name' => 'Admin',
        ],
    ]);
});

test('reports overview links every available financial report without invented totals', function () {
    $response = $this->get(route('reports.index'));

    $response
        ->assertOk()
        ->assertSee('Daily accounting report')
        ->assertSee(route('reports.daily'), false)
        ->assertSee('data-report-card="daily"', false)
        ->assertSee('Payment and transaction history')
        ->assertSee(route('reports.transactions'), false)
        ->assertSee('data-report-card="transactions"', false)
        ->assertSee('Expenses')
        ->assertSee(route('reports.expenses'), false)
        ->assertSee('data-report-card="expenses"', false)
        ->assertSee('Report data will become available when the backend is connected.')
        ->assertDontSee('₱')
        ->assertDontSee('PHP ')
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('daily accounting report exposes blank daily and multi-user filters with disabled actions', function () {
    $response = $this->get(route('reports.daily'));

    $response
        ->assertOk()
        ->assertSee('Report date')
        ->assertSee('name="report_date"', false)
        ->assertSee('type="date"', false)
        ->assertSee('Staff/User')
        ->assertSee('name="staff_users[]"', false)
        ->assertSee('multiple', false)
        ->assertDontSee('<option', false)
        ->assertSee('Staff options and report aggregation will be available when the backend is connected.')
        ->assertSee('Generate preview')
        ->assertSee('data-test="daily-generate-disabled"', false)
        ->assertSee('Print')
        ->assertSee('data-test="daily-print-disabled"', false)
        ->assertSee('Export')
        ->assertSee('data-test="daily-export-disabled"', false)
        ->assertSee('disabled', false)
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('daily accounting report renders the complete print-oriented preview structure', function () {
    $response = $this->get(route('reports.daily'));

    $response
        ->assertOk()
        ->assertSee('Accounting Daily Report')
        ->assertSee('Report date')
        ->assertSee('Prepared by / Selected staff')
        ->assertSee('Company')
        ->assertSee('Status')
        ->assertSee('Bill Collection Summary')
        ->assertSee('Payment type')
        ->assertSee('Amount')
        ->assertSee('Other Cash Collection Summary')
        ->assertSee('Expenses Breakdown')
        ->assertSee('Description')
        ->assertSee('Cash Status')
        ->assertSee('Total cash available')
        ->assertSee('Total expenses')
        ->assertSee('Cash on hand')
        ->assertSee('Remit')
        ->assertSee('Remarks')
        ->assertSee('Prepared by')
        ->assertSee('Checked by')
        ->assertSee('Approved by')
        ->assertDontSee('₱')
        ->assertDontSee('PHP ')
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('payment and transaction history provides chart filters and an empty scrollable table', function () {
    $response = $this->get(route('reports.transactions'));

    $response
        ->assertOk()
        ->assertSee('Payment activity')
        ->assertSee('Monthly')
        ->assertSee('Weekly')
        ->assertSee('Year')
        ->assertSee('Month')
        ->assertSee('Posting status')
        ->assertSee('Search')
        ->assertSee('Transaction type')
        ->assertSee('Timestamp')
        ->assertSee('Account details')
        ->assertSee('Transaction')
        ->assertSee('Receipt information')
        ->assertSee('Reference')
        ->assertSee('Updated by')
        ->assertSee('No payment or transaction records yet')
        ->assertSee('data-table-scroll', false)
        ->assertSee('overflow-x-auto', false)
        ->assertDontSee('₱')
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('expenses report provides chart filters and an empty scrollable table', function () {
    $response = $this->get(route('reports.expenses'));

    $response
        ->assertOk()
        ->assertSee('Expense activity')
        ->assertSee('Monthly')
        ->assertSee('Weekly')
        ->assertSee('Year')
        ->assertSee('Month')
        ->assertSee('Search')
        ->assertSee('Add expense')
        ->assertSee('data-test="expense-add-disabled"', false)
        ->assertSee('Timestamp')
        ->assertSee('Invoice')
        ->assertSee('Supplier / description')
        ->assertSee('Amount')
        ->assertSee('Updated by')
        ->assertSee('No expense records yet')
        ->assertSee('data-table-scroll', false)
        ->assertSee('overflow-x-auto', false)
        ->assertDontSee('₱')
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('system logs provide visual filters and an empty scrollable table', function () {
    $response = $this->get(route('logs.system'));

    $response
        ->assertOk()
        ->assertSee('Year')
        ->assertSee('Log type')
        ->assertSee('Entries per page')
        ->assertSee('Search')
        ->assertSee('Export')
        ->assertSee('data-test="system-log-export-disabled"', false)
        ->assertSee('Pagination')
        ->assertSee('Timestamp')
        ->assertSee('Description')
        ->assertSee('No system logs yet')
        ->assertSee('data-table-scroll', false)
        ->assertSee('overflow-x-auto', false)
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});

test('activity logs provide chart filters and an empty scrollable notes table', function () {
    $response = $this->get(route('logs.activity'));

    $response
        ->assertOk()
        ->assertSee('Activity notes')
        ->assertSee('Monthly')
        ->assertSee('Weekly')
        ->assertSee('Year')
        ->assertSee('Type')
        ->assertSee('Add note')
        ->assertSee('data-test="activity-add-disabled"', false)
        ->assertSee('Select')
        ->assertSee('Timestamp')
        ->assertSee('Information')
        ->assertSee('Updated by')
        ->assertSee('No activity notes yet')
        ->assertSee('data-table-scroll', false)
        ->assertSee('overflow-x-auto', false)
        ->assertDontSee('<img', false)
        ->assertDontSee('<svg', false);
});
