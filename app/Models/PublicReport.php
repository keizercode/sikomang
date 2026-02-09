<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mangrove_location_id',
        'report_number',
        'description',
        'report_type',
        'urgency_level',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'reporter_address',
        'reporter_organization',
        'photo_urls',
        'status',
        'admin_notes',
        'verified_by',
        'verified_at',
        'resolved_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'photo_urls' => 'array',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate report number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->report_number)) {
                $report->report_number = self::generateReportNumber();
            }
        });
    }

    /**
     * Generate unique report number
     * Format: REP-YYYYMMDD-XXXXX
     */
    public static function generateReportNumber()
    {
        $date = now()->format('Ymd');
        $prefix = "REP-{$date}-";

        // Get last report number for today
        $lastReport = self::where('report_number', 'like', $prefix . '%')
            ->orderBy('report_number', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = intval(substr($lastReport->report_number, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Relationship with MangroveLocation
     */
    public function location()
    {
        return $this->belongsTo(MangroveLocation::class, 'mangrove_location_id');
    }

    /**
     * Relationship with User (verifier)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified reports
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for resolved reports
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope for active reports (not resolved or rejected)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['resolved', 'rejected']);
    }

    /**
     * Get urgency color for badge
     */
    public function getUrgencyColorAttribute()
    {
        return [
            'rendah' => 'info',
            'sedang' => 'warning',
            'tinggi' => 'danger',
            'darurat' => 'dark'
        ][$this->urgency_level] ?? 'secondary';
    }

    /**
     * Get status color for badge
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'verified' => 'info',
            'in_review' => 'primary',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'rejected' => 'danger'
        ][$this->status] ?? 'secondary';
    }

    /**
     * Get status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'in_review' => 'Sedang Ditinjau',
            'in_progress' => 'Sedang Ditangani',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ][$this->status] ?? $this->status;
    }

    /**
     * Get urgency label in Indonesian
     */
    public function getUrgencyLabelAttribute()
    {
        return [
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            'darurat' => 'Darurat'
        ][$this->urgency_level] ?? $this->urgency_level;
    }

    /**
     * Get report type label in Indonesian
     */
    public function getReportTypeLabelAttribute()
    {
        return [
            'kerusakan' => 'Kerusakan',
            'pencemaran' => 'Pencemaran',
            'penebangan_liar' => 'Penebangan Liar',
            'kondisi_baik' => 'Kondisi Baik',
            'lainnya' => 'Lainnya'
        ][$this->report_type] ?? $this->report_type;
    }

    /**
     * Check if report has photos
     */
    public function hasPhotos()
    {
        return !empty($this->photo_urls) && is_array($this->photo_urls) && count($this->photo_urls) > 0;
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d F Y, H:i');
    }

    /**
     * Check if report is urgent
     */
    public function isUrgent()
    {
        return in_array($this->urgency_level, ['tinggi', 'darurat']);
    }

    /**
     * Check if report is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if report is resolved
     */
    public function isResolved()
    {
        return $this->status === 'resolved';
    }
}
