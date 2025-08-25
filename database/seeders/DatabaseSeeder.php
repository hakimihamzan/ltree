<?php

namespace Database\Seeders;

use App\Models\ApprovalChain;
use App\Models\Department;
use App\Models\User;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestApprover;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data (PostgreSQL compatible)
        PurchaseRequestApprover::truncate();
        PurchaseRequest::truncate();
        ApprovalChain::truncate();
        User::truncate();
        Department::truncate();

        // Create Departments
        $finance = Department::create([
            'name' => 'Finance Department'
        ]);

        $it = Department::create([
            'name' => 'IT Department'
        ]);

        $hr = Department::create([
            'name' => 'Human Resources'
        ]);

        // Create Users for Finance Department
        $users = [];

        // Finance Department Users
        $users['finance_cfo'] = User::create(['name' => 'Sarah Johnson', 'email' => 'sarah.johnson@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $users['finance_director'] = User::create(['name' => 'Michael Chen', 'email' => 'michael.chen@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $users['finance_manager'] = User::create(['name' => 'Lisa Wang', 'email' => 'lisa.wang@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $users['finance_senior'] = User::create(['name' => 'Robert Davis', 'email' => 'robert.davis@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $users['finance_analyst1'] = User::create(['name' => 'Emma Thompson', 'email' => 'emma.thompson@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);
        $users['finance_analyst2'] = User::create(['name' => 'James Wilson', 'email' => 'james.wilson@company.com', 'password' => bcrypt('password'), 'department_id' => $finance->id]);

        // IT Department Users
        $users['it_cto'] = User::create(['name' => 'David Rodriguez', 'email' => 'david.rodriguez@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_director'] = User::create(['name' => 'Jennifer Lee', 'email' => 'jennifer.lee@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_dev_manager'] = User::create(['name' => 'Alex Kumar', 'email' => 'alex.kumar@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_ops_manager'] = User::create(['name' => 'Maria Garcia', 'email' => 'maria.garcia@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_senior_dev'] = User::create(['name' => 'Kevin Chang', 'email' => 'kevin.chang@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_dev1'] = User::create(['name' => 'Amanda White', 'email' => 'amanda.white@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_dev2'] = User::create(['name' => 'Ryan Martinez', 'email' => 'ryan.martinez@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);
        $users['it_sysadmin'] = User::create(['name' => 'Sophie Brown', 'email' => 'sophie.brown@company.com', 'password' => bcrypt('password'), 'department_id' => $it->id]);

        // HR Department Users
        $users['hr_vp'] = User::create(['name' => 'Patricia Anderson', 'email' => 'patricia.anderson@company.com', 'password' => bcrypt('password'), 'department_id' => $hr->id]);
        $users['hr_director'] = User::create(['name' => 'Thomas Clark', 'email' => 'thomas.clark@company.com', 'password' => bcrypt('password'), 'department_id' => $hr->id]);
        $users['hr_manager'] = User::create(['name' => 'Rachel Green', 'email' => 'rachel.green@company.com', 'password' => bcrypt('password'), 'department_id' => $hr->id]);
        $users['hr_specialist1'] = User::create(['name' => 'Daniel Kim', 'email' => 'daniel.kim@company.com', 'password' => bcrypt('password'), 'department_id' => $hr->id]);
        $users['hr_specialist2'] = User::create(['name' => 'Laura Miller', 'email' => 'laura.miller@company.com', 'password' => bcrypt('password'), 'department_id' => $hr->id]);

        // Create Approval Chains using actual user IDs in paths

        // Finance Department Hierarchy
        // CFO (Level 1) -> Director (Level 2) -> Manager (Level 3) -> Senior Analyst (Level 4) -> Analysts (Level 5)
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_cfo']->id, 'path' => $users['finance_cfo']->id]); // CFO
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_director']->id, 'path' => $users['finance_cfo']->id . '.' . $users['finance_director']->id]); // Director
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_manager']->id, 'path' => $users['finance_cfo']->id . '.' . $users['finance_director']->id . '.' . $users['finance_manager']->id]); // Manager
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_senior']->id, 'path' => $users['finance_cfo']->id . '.' . $users['finance_director']->id . '.' . $users['finance_manager']->id . '.' . $users['finance_senior']->id]); // Senior Analyst
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_analyst1']->id, 'path' => $users['finance_cfo']->id . '.' . $users['finance_director']->id . '.' . $users['finance_manager']->id . '.' . $users['finance_senior']->id . '.' . $users['finance_analyst1']->id]); // Analyst 1
        ApprovalChain::create(['department_id' => $finance->id, 'user_id' => $users['finance_analyst2']->id, 'path' => $users['finance_cfo']->id . '.' . $users['finance_director']->id . '.' . $users['finance_manager']->id . '.' . $users['finance_senior']->id . '.' . $users['finance_analyst2']->id]); // Analyst 2

        // IT Department Hierarchy
        // CTO (Level 1) -> Director (Level 2) -> Managers (Level 3) -> Senior/Team Leads (Level 4) -> Developers (Level 5)
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_cto']->id, 'path' => $users['it_cto']->id]); // CTO
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_director']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id]); // Director
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_dev_manager']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_dev_manager']->id]); // Dev Manager
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_ops_manager']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_ops_manager']->id]); // Ops Manager
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_senior_dev']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_dev_manager']->id . '.' . $users['it_senior_dev']->id]); // Senior Dev
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_dev1']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_dev_manager']->id . '.' . $users['it_senior_dev']->id . '.' . $users['it_dev1']->id]); // Developer 1
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_dev2']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_dev_manager']->id . '.' . $users['it_senior_dev']->id . '.' . $users['it_dev2']->id]); // Developer 2
        ApprovalChain::create(['department_id' => $it->id, 'user_id' => $users['it_sysadmin']->id, 'path' => $users['it_cto']->id . '.' . $users['it_director']->id . '.' . $users['it_ops_manager']->id . '.' . $users['it_sysadmin']->id]); // SysAdmin

        // HR Department Hierarchy
        // VP (Level 1) -> Director (Level 2) -> Manager (Level 3) -> Specialists (Level 4)
        ApprovalChain::create(['department_id' => $hr->id, 'user_id' => $users['hr_vp']->id, 'path' => $users['hr_vp']->id]); // VP
        ApprovalChain::create(['department_id' => $hr->id, 'user_id' => $users['hr_director']->id, 'path' => $users['hr_vp']->id . '.' . $users['hr_director']->id]); // Director
        ApprovalChain::create(['department_id' => $hr->id, 'user_id' => $users['hr_manager']->id, 'path' => $users['hr_vp']->id . '.' . $users['hr_director']->id . '.' . $users['hr_manager']->id]); // Manager
        ApprovalChain::create(['department_id' => $hr->id, 'user_id' => $users['hr_specialist1']->id, 'path' => $users['hr_vp']->id . '.' . $users['hr_director']->id . '.' . $users['hr_manager']->id . '.' . $users['hr_specialist1']->id]); // Specialist 1
        ApprovalChain::create(['department_id' => $hr->id, 'user_id' => $users['hr_specialist2']->id, 'path' => $users['hr_vp']->id . '.' . $users['hr_director']->id . '.' . $users['hr_manager']->id . '.' . $users['hr_specialist2']->id]); // Specialist 2

        // Create Purchase Requests with realistic scenarios
        $purchaseRequests = [
            // Finance Department Requests
            [
                'item' => 'New Accounting Software License - Annual subscription for QuickBooks Enterprise for 50 users',
                'amount' => 15000.00,
                'department_id' => $finance->id,
                'requester_id' => $users['finance_analyst1']->id, // Emma Thompson (Analyst)
                'status' => 'pending',
                'current_approval_level' => 1,
            ],
            [
                'item' => 'Office Supplies - Q1 2025 - Bulk order of office supplies including paper, pens, folders, etc.',
                'amount' => 2500.00,
                'department_id' => $finance->id,
                'requester_id' => $users['finance_analyst2']->id, // James Wilson (Analyst)
                'status' => 'approved',
                'current_approval_level' => 5,
            ],
            [
                'item' => 'Financial Audit Services - External audit services for fiscal year 2024',
                'amount' => 45000.00,
                'department_id' => $finance->id,
                'requester_id' => $users['finance_manager']->id, // Lisa Wang (Manager)
                'status' => 'pending',
                'current_approval_level' => 2,
            ],

            // IT Department Requests
            [
                'item' => 'New Development Laptops - 5 high-performance laptops for development team with SSD and 32GB RAM',
                'amount' => 12000.00,
                'department_id' => $it->id,
                'requester_id' => $users['it_senior_dev']->id, // Kevin Chang (Senior Dev)
                'status' => 'pending',
                'current_approval_level' => 1,
            ],
            [
                'item' => 'Cloud Infrastructure Upgrade - AWS services upgrade for increased storage and computing power',
                'amount' => 8500.00,
                'department_id' => $it->id,
                'requester_id' => $users['it_sysadmin']->id, // Sophie Brown (SysAdmin)
                'status' => 'approved',
                'current_approval_level' => 4,
            ],
            [
                'item' => 'Software Development Tools - JetBrains licenses and other development tools for the team',
                'amount' => 3200.00,
                'department_id' => $it->id,
                'requester_id' => $users['it_dev1']->id, // Amanda White (Developer)
                'status' => 'pending',
                'current_approval_level' => 3,
            ],
            [
                'item' => 'Network Security Upgrade - Firewall hardware and security monitoring software',
                'amount' => 25000.00,
                'department_id' => $it->id,
                'requester_id' => $users['it_ops_manager']->id, // Maria Garcia (Ops Manager)
                'status' => 'approved',
                'current_approval_level' => 3,
            ],

            // HR Department Requests
            [
                'item' => 'Employee Training Program - Leadership development program for managers and supervisors',
                'amount' => 18000.00,
                'department_id' => $hr->id,
                'requester_id' => $users['hr_specialist1']->id, // Daniel Kim (Specialist)
                'status' => 'pending',
                'current_approval_level' => 2,
            ],
            [
                'item' => 'Recruitment Software License - Annual license for applicant tracking system',
                'amount' => 6000.00,
                'department_id' => $hr->id,
                'requester_id' => $users['hr_specialist2']->id, // Laura Miller (Specialist)
                'status' => 'approved',
                'current_approval_level' => 4,
            ],
            [
                'item' => 'Office Furniture for New Hires - Desks, chairs, and storage units for 10 new employees',
                'amount' => 8000.00,
                'department_id' => $hr->id,
                'requester_id' => $users['hr_manager']->id, // Rachel Green (Manager)
                'status' => 'pending',
                'current_approval_level' => 1,
            ],
        ];

        foreach ($purchaseRequests as $index => $requestData) {
            $request = PurchaseRequest::create($requestData);

            // Initialize approval process for each request
            $request->initializeApprovalProcess();

            // Simulate some approvals for approved requests
            if ($requestData['status'] === 'approved') {
                $this->simulateApprovals($request);
            } elseif ($requestData['status'] === 'pending' && $requestData['current_approval_level'] > 1) {
                $this->simulatePartialApprovals($request, $requestData['current_approval_level']);
            }
        }

        echo "Database seeded successfully!\n";
        echo "Created:\n";
        echo "- 3 Departments\n";
        echo "- 18 Users\n";
        echo "- 16 Approval Chains\n";
        echo "- 10 Purchase Requests\n";
    }

    private function simulateApprovals(PurchaseRequest $request)
    {
        // Get all approvers for this request and mark them as approved
        $approvers = $request->approvers()->get();
        foreach ($approvers as $approver) {
            $approver->update(['has_approved' => true]);
        }
    }

    private function simulatePartialApprovals(PurchaseRequest $request, $targetLevel)
    {
        for ($level = 1; $level < $targetLevel; $level++) {
            $levelApprovers = $request->approvers()
                ->whereHas('approvalChain', function ($query) use ($level) {
                    $query->whereRaw('nlevel(path) = ?', [$level]);
                })->get();

            if ($levelApprovers->isNotEmpty()) {
                // Approve one approver at this level (simulate OR logic)
                $levelApprovers->first()->update(['has_approved' => true]);

                // Move to next level if possible
                if ($request->canMoveToNextLevel()) {
                    $request->moveToNextLevel();
                }
            }
        }
    }
}
