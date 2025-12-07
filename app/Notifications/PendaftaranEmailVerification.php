<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PendaftaranMahasiswa;

class PendaftaranEmailVerification extends Notification
{
    use Queueable;

    protected $pendaftaran;
    protected $verificationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(PendaftaranMahasiswa $pendaftaran, string $verificationUrl)
    {
        $this->pendaftaran = $pendaftaran;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Pendaftaran - ' . config('app.name'))
            ->greeting('Halo ' . $this->pendaftaran->nama_lengkap . ',')
            ->line('Terima kasih telah mendaftar di ' . config('app.name') . '.')
            ->line('Nomor Pendaftaran Anda: **' . $this->pendaftaran->no_pendaftaran . '**')
            ->line('Program Studi: **' . $this->pendaftaran->programStudi->nama_prodi . '**')
            ->line('Silakan klik tombol di bawah untuk memverifikasi alamat email Anda:')
            ->action('Verifikasi Email', $this->verificationUrl)
            ->line('Link verifikasi ini akan kadaluarsa dalam 24 jam.')
            ->line('Jika Anda tidak mendaftar, abaikan email ini.')
            ->salutation('Hormat kami, Tim ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pendaftaran_id' => $this->pendaftaran->id,
            'no_pendaftaran' => $this->pendaftaran->no_pendaftaran,
        ];
    }
}
