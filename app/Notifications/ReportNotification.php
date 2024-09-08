<?php

namespace App\Notifications;

use App\Models\LabManagement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $labManagement;
    public $pegawaiPenyemakName;
    public $submitterName;

    public function __construct(LabManagement $labManagement, $pegawaiPenyemakName, $submitterName)
    {
        $this->labManagement = $labManagement;
        $this->pegawaiPenyemakName = $pegawaiPenyemakName;
        $this->submitterName = $submitterName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $campus = $this->labManagement->computerLab->campus->name;
        $computerLab = $this->labManagement->computerLab->name;
        $subject = 'Laporan Selenggara: ' . $computerLab . ' '. $campus 
            . ' ' . '(' . date('F', strtotime($this->labManagement->start_time))
            . '/' . date('Y', strtotime($this->labManagement->start_time)) . ')';

        return (new MailMessage)
            ->subject($subject)
            ->view('pages.lab-management.emails.report-notification', [
                'labManagement' => $this->labManagement,
                'pegawaiPenyemakName' => $this->pegawaiPenyemakName,
                'submitterName' => $this->submitterName,
                'emailSubject' => $subject,
                'month' => date('F', strtotime($this->labManagement->start_time)),
                'year' => date('Y', strtotime($this->labManagement->start_time)), 
                'campus' => $campus,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
