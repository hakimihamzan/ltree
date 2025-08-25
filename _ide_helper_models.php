<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $department_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $path
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalChain whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperApprovalChain {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApprovalChain> $approvalChains
 * @property-read int|null $approval_chains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseRequest> $purchaseRequests
 * @property-read int|null $purchase_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDepartment {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $department_id
 * @property int $requester_id
 * @property string $item
 * @property string $amount
 * @property int $current_approval_level
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseRequestApprover> $approvers
 * @property-read int|null $approvers_count
 * @property-read \App\Models\Department $department
 * @property-read \App\Models\User $requester
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereCurrentApprovalLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPurchaseRequest {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $purchase_request_id
 * @property int $approval_chain_id
 * @property bool|null $has_approved
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ApprovalChain $approvalChain
 * @property-read \App\Models\PurchaseRequest $purchaseRequest
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereApprovalChainId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereHasApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover wherePurchaseRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseRequestApprover whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPurchaseRequestApprover {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $department_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApprovalChain> $approvalChains
 * @property-read int|null $approval_chains_count
 * @property-read \App\Models\Department|null $department
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseRequestApprover> $pendingApprovals
 * @property-read int|null $pending_approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseRequest> $submittedPurchaseRequests
 * @property-read int|null $submitted_purchase_requests_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

