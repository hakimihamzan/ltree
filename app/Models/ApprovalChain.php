<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * @mixin IdeHelperApprovalChain
 */
class ApprovalChain extends Model
{
    protected $guarded = ['id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Get next approver level (direct subordinates only)
    public function nextApprovers(): Collection
    {
        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('path <@ ? AND nlevel(path) = nlevel(?) + 1', [$this->path, $this->path])
            ->get();
    }

    // Get previous approver level
    public function previousApprovers(): Collection
    {
        $prevLevel = $this->getLevel() - 1;
        if ($prevLevel < 1) return new Collection(); // Level 1 is first approver level

        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = ?', [$prevLevel])
            ->get();
    }

    // Get the level of this approver in the hierarchy
    public function getLevel(): int
    {
        return substr_count($this->path ?? '', '.') + 1; // +1 because path "1" is level 1, "1.2" is level 2
    }

    // Get all approvers at the same level
    public function sameLevelApprovers(): Collection
    {
        return ApprovalChain::where('department_id', $this->department_id)
            ->whereRaw('nlevel(path) = nlevel(?)', [$this->path])
            ->where('id', '!=', $this->id)
            ->get();
    }
}
