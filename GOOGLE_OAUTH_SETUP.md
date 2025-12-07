# Setup Google OAuth untuk SISTER

## Langkah 1: Buat Google Cloud Project

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Klik **Select a project** → **New Project**
3. Nama project: `SISTER - Sistem Akademik`
4. Klik **Create**

## Langkah 2: Enable Google+ API

1. Di sidebar, pilih **APIs & Services** → **Library**
2. Cari **Google+ API**
3. Klik **Enable**

## Langkah 3: Configure OAuth Consent Screen

1. Di sidebar, pilih **APIs & Services** → **OAuth consent screen**
2. Pilih **External** → Klik **Create**
3. Isi form:
   - **App name**: SISTER - Sistem Akademik
   - **User support email**: email Anda
   - **Developer contact**: email Anda
4. Klik **Save and Continue**
5. **Scopes**: Klik **Add or Remove Scopes**
   - Pilih: `.../auth/userinfo.email`
   - Pilih: `.../auth/userinfo.profile`
6. Klik **Save and Continue**
7. **Test users** (untuk development):
   - Tambahkan email yang akan digunakan untuk testing
8. Klik **Save and Continue**

## Langkah 4: Buat OAuth 2.0 Credentials

1. Di sidebar, pilih **APIs & Services** → **Credentials**
2. Klik **Create Credentials** → **OAuth client ID**
3. Application type: **Web application**
4. Name: `SISTER Web Client`
5. **Authorized JavaScript origins**:
   ```
   http://sister.test
   http://localhost
   ```
6. **Authorized redirect URIs**:
   ```
   http://sister.test/auth/google/callback
   http://localhost/auth/google/callback
   ```
7. Klik **Create**
8. **PENTING**: Copy **Client ID** dan **Client Secret**

## Langkah 5: Update .env File

Buka file `.env` di root project dan update:

```env
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://sister.test/auth/google/callback
```

## Langkah 6: Clear Config Cache

```bash
php artisan config:clear
```

## Testing

1. Buka http://sister.test/login
2. Klik tombol **Login dengan Google**
3. Login dengan akun Google yang sudah ditambahkan sebagai Test User
4. Setelah berhasil, akan redirect ke dashboard

## Catatan Penting

### Development Mode
- Google OAuth dalam mode **Testing** hanya bisa diakses oleh Test Users yang sudah didaftarkan
- Maksimal 100 test users

### Production Mode
- Untuk production, OAuth app perlu **Verification** dari Google
- Submit app untuk review di OAuth consent screen
- Proses review bisa 1-2 minggu

### Security
- **JANGAN** commit file `.env` ke Git
- **JANGAN** share Client Secret
- Untuk production, gunakan HTTPS (https://)

## Troubleshooting

### Error: redirect_uri_mismatch
- Pastikan redirect URI di Google Console sama persis dengan yang di `.env`
- Termasuk trailing slash (/)

### Error: Access blocked
- Pastikan email Anda sudah ditambahkan sebagai Test User
- Pastikan OAuth consent screen sudah configured

### Error: Invalid client
- Periksa kembali GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di `.env`
- Pastikan tidak ada spasi atau karakter hidden

## Fitur yang Sudah Diimplementasi

✅ Login with Google button di halaman login
✅ Auto-create user baru dari Google account
✅ Link Google account ke user existing (by email)
✅ Simpan avatar dari Google
✅ Auto-verified email
✅ Assign role default "Mahasiswa" untuk user baru
✅ Update avatar setiap login

## Database Changes

Kolom baru di table `users`:
- `google_id` (string, nullable, unique) - Google user ID
- `avatar` (string, nullable) - URL foto profil dari Google
