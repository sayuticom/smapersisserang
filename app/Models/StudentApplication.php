<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    use HasFactory;

    protected $table = 'student_applications';
    
    protected $fillable = [
        'registration_number', 'admission_year_id', 'admission_program_id', 'status',
        'student_name', 'gender', 'birth_place', 'birth_date', 'previous_school',
        'address', 'father_name', 'mother_name', 'parent_whatsapp', 'parent_job',
        'boarding_ready', 'quran_reading_ability', 'health_notes', 'motivation',
        'admin_notes', 'verified_by', 'verified_at', 'submitted_at',
        'follow_up_status', 'follow_up_notes', 'follow_up_at', 'follow_up_by'
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'boarding_ready' => 'boolean',
        'follow_up_at' => 'datetime',
    ];
    
    public function admissionYear()
    {
        return $this->belongsTo(AdmissionYear::class);
    }
    
    public function admissionProgram()
    {
        return $this->belongsTo(AdmissionProgram::class);
    }
    
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    public function statusHistories()
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }
    
    public function generateRegistrationNumber()
    {
        $year = $this->admission_year->academic_year;
        $count = StudentApplication::where('admission_year_id', $this->admission_year_id)->count();
        $paddedCount = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return "SPMB-{$year}-{$paddedCount}";
    }
    
    public function isPending()
    {
        return in_array($this->status, ['baru_daftar', 'menunggu_verifikasi', 'data_kurang']);
    }
    
    public function isVerified()
    {
        return $this->status === 'terverifikasi';
    }
    
    public function isAdmitted()
    {
        return $this->status === 'diterima';
    }
    
    public function isActive()
    {
        return $this->status === 'terverifikasi';
    }

    public function followUpBy()
    {
        return $this->belongsTo(User::class, 'follow_up_by');
    }
}