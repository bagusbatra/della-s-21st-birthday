export const INITIAL_MEMORIES = [
  {
    id: 'm1',
    url: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=800&q=80',
    caption: 'Senyum manismu yang selalu menenangkan setiap hariku',
    date: '14 Februari',
    location: 'Café Kenangan, Sudirman',
    tag: 'Momen Manis',
    note: 'Hari itu kamu mengenakan baju favoritmu dan tertawa renyah saat menceritakan mimpimu.',
    likes: 21
  },
  {
    id: 'm2',
    url: 'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?auto=format&fit=crop&w=800&q=80',
    caption: 'Di bawah langit senja, Della terlihat sangat menawan',
    date: '28 Mei',
    location: 'Pantai Indah Kapuk',
    tag: 'Kencan Spesial',
    note: 'Angin laut menerbangkan rambutmu, dan aku menyadari betapa beruntungnya aku memilikimu.',
    likes: 18
  },
  {
    id: 'm3',
    url: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
    caption: 'Buket bunga kecil untuk perempuan paling istimewa',
    date: '10 Juli',
    location: 'Taman Bunga Kota',
    tag: 'Kejutan Kecil',
    note: 'Ekspresi terkejut dan bahagia di wajahmu adalah pemandangan terbaik di dunia.',
    likes: 25
  },
  {
    id: 'm4',
    url: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80',
    caption: 'Tawa lepas bersamamu yang tak pernah pudar',
    date: '19 September',
    location: 'Perpustakaan & Toko Buku',
    tag: 'Kenangan Hangat',
    note: 'Kita bisa menghabiskan berjam-jam hanya berbicara tentang hal-hal kecil tanpa pernah bosan.',
    likes: 19
  },
  {
    id: 'm5',
    url: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80',
    caption: 'Tatapan matamu yang selalu memberi semangat terbesar',
    date: '25 November',
    location: 'Rooftop City Lights',
    tag: 'Momen Manis',
    note: 'Di antara gemerlap lampu kota, hanya pesonamu yang paling bersinar terang bagiku.',
    likes: 32
  },
  {
    id: 'm6',
    url: 'https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=800&q=80',
    caption: 'Genggaman tangan yang akan selalu kujaga selamanya',
    date: '1 Januari',
    location: 'Awal Tahun Baru',
    tag: 'Janji Hati',
    note: 'Melangkah menyambut tahun baru dan masa depan yang penuh harapan bersamamu.',
    likes: 40
  }
];

export const INITIAL_SECRET_WISHES = [
  {
    id: 'w1',
    sender: 'Mama & Papa',
    role: 'Keluarga Tersayang',
    avatarEmoji: '🌸',
    envelopeColor: 'rose',
    message: 'Selamat ulang tahun ke-21 putri tercinta Della Puspa Ardiati. Semoga berkah umur, sehat selalu, dilancarkan jalan menuju cita-citamu, dan senantiasa menjadi kebanggaan keluarga. Doa terbaik Mama dan Papa selalu menyertaimu. 🎂🤲',
    timestamp: 'Baru saja',
    hint: 'Doa hangat penuh kasih dari orang tua',
    isOpened: false,
    likes: 12
  },
  {
    id: 'w2',
    sender: 'Maya & Salsa',
    role: 'Bestie Kampus',
    avatarEmoji: '✨',
    envelopeColor: 'amber',
    message: 'Happy 21st Birthday Della sayang! Semoga di usia 21 ini kuliah lancar jaya, makin glowing, selalu bahagia, dan langgeng terus sama ayang! We love you so much! Jangan lupa traktirannya yaa! 💖🥳',
    timestamp: '1 jam lalu',
    hint: 'Pesan dari sahabat satu geng kampusmu!',
    isOpened: false,
    likes: 8
  },
  {
    id: 'w3',
    sender: 'Kak Dimas & Kak Rara',
    role: 'Kakak Tersayang',
    avatarEmoji: '💌',
    envelopeColor: 'pink',
    message: 'Happy Level 21 adik kami tersayang! Sukses untuk semua rencana hebat di usia kepala dua satu ini. Semoga karir dan impian masa depan tercapai satu per satu dengan mudah!',
    timestamp: '2 jam lalu',
    hint: 'Semangat dari kakak-kakak terhebat',
    isOpened: false,
    likes: 6
  },
  {
    id: 'w4',
    sender: 'Nisa Rahmawati',
    role: 'Teman Curhat SMA',
    avatarEmoji: '🌷',
    envelopeColor: 'purple',
    message: 'Dellaaa! Selamat 21 tahun! Gak kerasa ya dari jaman seragam putih abu-abu sekarang udah dewasa banget. Tetep jadi Della yang ceria, penyayang, dan pendengar yang baik ya. Miss you loads! 🥰',
    timestamp: '3 jam lalu',
    hint: 'Teman seperjuangan masa putih abu-abu',
    isOpened: false,
    likes: 9
  },
  {
    id: 'w5',
    sender: 'Circle Sahabat Seperjuangan',
    role: 'Teman Hangout',
    avatarEmoji: '🎉',
    envelopeColor: 'indigo',
    message: 'Barakallah fii umrik Della Puspa Ardiati! Tetap menjadi sosok yang menginspirasi, lembut hatinya, dan selalu membawa aura positif ke manapun Della melangkah.',
    timestamp: '4 jam lalu',
    hint: 'Pesan manis penuh ketulusan',
    isOpened: false,
    likes: 7
  },
  {
    id: 'w6',
    sender: 'Kekasih Hati',
    role: 'Selamanya Milikmu',
    avatarEmoji: '❤️',
    envelopeColor: 'emerald',
    message: 'Terima kasih telah hadir membawa warna terindah di hidupku. Selamat ulang tahun ke-21 bidadariku, aku mencintaimu hari ini, esok, dan selamanya.',
    timestamp: 'Spesial',
    hint: 'Pesan rahasia dari seseorang yang paling mencintaimu',
    isOpened: false,
    likes: 21
  }
];

export const DEFAULT_LOVE_LETTER = {
  recipient: 'Della Puspa Ardiati',
  salutation: 'Untuk Kekasih Terindahku, Della Puspa Ardiati,',
  paragraphs: [
    'Selamat ulang tahun yang ke-21, bidadari hatiku. Hari ini adalah hari di mana semesta menghadiahkan seseorang yang begitu cantik, berhati mulia, dan penuh cahaya ke dunia ini—dan aku bersyukur kepada Tuhan karena telah mempertemukanku denganmu.',
    'Dua puluh satu tahun adalah usia yang sangat indah, sebuah gerbang menuju kedewasaan, mimpi-mimpi besar, dan petualangan baru. Menyaksikanmu tumbuh menjadi wanita yang anggun, cerdas, dan tangguh adalah salah satu kebanggaan terbesar dalam hidupku.',
    'Terima kasih telah mewarnai setiap hariku dengan senyumanmu yang manis, tawamu yang renyah, dan pelukan hangatmu saat aku lelah. Di setiap langkah yang akan kamu ambil ke depan, ketahuilah bahwa tanganku akan selalu siap menggenggammu, pundakku akan selalu ada untukmu bersandar, dan hatiku akan selalu menjadi tempatmu pulang.',
    'Semoga di usia 21 tahun ini, Allah SWT senantiasa melimpahkan kesehatan, kebahagiaan tanpa akhir, kemudahan dalam setiap urusan, dan tercapainya segala impian muliamu. Tetaplah menjadi Della yang selalu rendah hati, mempesona, dan membawa kehangatan bagi siapa pun.',
    'Selamat bertambah usia, cintaku. Aku mencintaimu lebih dari kata-kata yang sanggup kuukirkan di sini, hari ini dan untuk selamanya.'
  ],
  closing: 'Dengan segenap cinta dan ketulusan hati,',
  sender: 'Kekasihmu yang Selalu Menyayangimu ❤️'
};
