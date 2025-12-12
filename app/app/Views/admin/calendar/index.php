<?= $this->extend('admin/layout/header') ?>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0"><i class="bi bi-calendar-week"></i> Kalender Acara</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pesanan') ?>">Pesanan</a></li>
                    <li class="breadcrumb-item active">Kalender</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-outline-primary">
                <i class="bi bi-list-ul"></i> Daftar Pesanan
            </a>
        </div>
    </div>

    <!-- Statistik & Filter -->
    <div class="row mb-4">
        <!-- Statistik -->
        <div class="col-lg-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border-start border-primary border-4 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted fw-semibold">Total Acara</div>
                                    <div class="fw-bold fs-5"><?= $stats['total'] ?></div>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card border-start border-warning border-4 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted fw-semibold">Hari Ini</div>
                                    <div class="fw-bold fs-5"><?= $stats['today'] ?></div>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-calendar-day text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-3">
                    <div class="card border-start border-success border-4 h-100">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="text-muted fw-semibold">7 Hari ke Depan</div>
                                    <div class="fw-bold fs-5"><?= $stats['next_7_days'] ?></div>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-calendar-range text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filter Bulan & Tahun -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3"><i class="bi bi-calendar-month"></i> Pilih Periode</h6>
                    <form method="get" action="<?= base_url('admin/calendar') ?>" id="month-year-form">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select name="month" class="form-select form-select-sm" id="month-select">
                                    <?php foreach($months as $key => $monthName): ?>
                                        <option value="<?= $key ?>" <?= $selectedMonth == $key ? 'selected' : '' ?>>
                                            <?= $monthName ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <select name="year" class="form-select form-select-sm" id="year-select">
                                    <?php foreach($years as $year): ?>
                                        <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-search"></i> Tampilkan
                            </button>
                            <a href="<?= base_url('admin/calendar') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-calendar-day"></i> Bulan Ini
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Periode Terpilih -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">
                        <i class="bi bi-calendar3"></i> 
                        Periode: <strong><?= $months[$selectedMonth] ?> <?= $selectedYear ?></strong>
                    </h6>
                    <small class="text-muted">
                        <?= count($events) ?> acara ditemukan
                    </small>
                </div>
                <div>
                    <!-- Navigation buttons -->
                    <div class="btn-group btn-group-sm" role="group">
                        <?php
                        // Previous month
                        $prevMonth = $selectedMonth - 1;
                        $prevYear = $selectedYear;
                        if ($prevMonth < 1) {
                            $prevMonth = 12;
                            $prevYear--;
                        }
                        
                        // Next month
                        $nextMonth = $selectedMonth + 1;
                        $nextYear = $selectedYear;
                        if ($nextMonth > 12) {
                            $nextMonth = 1;
                            $nextYear++;
                        }
                        ?>
                        
                        <a href="<?= base_url("admin/calendar?year={$prevYear}&month={$prevMonth}") ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-chevron-left"></i> Bulan Sebelumnya
                        </a>
                        
                        <a href="<?= base_url('admin/calendar') ?>" class="btn btn-outline-secondary">
                            Bulan Ini
                        </a>
                        
                        <a href="<?= base_url("admin/calendar?year={$nextYear}&month={$nextMonth}") ?>" 
                           class="btn btn-outline-primary">
                            Bulan Berikutnya <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kalender & Daftar Acara -->
    <div class="row">
        <!-- Kalender View -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-calendar3"></i> Kalender</h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toggle-calendar-view">
                        <label class="form-check-label" for="toggle-calendar-view">Tampilan Daftar</label>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Calendar Container -->
                    <div id="calendar-container">
                        <div id="calendar"></div>
                    </div>
                    
                    <!-- List View Container (hidden by default) -->
                    <!-- List View Container (hidden by default) -->
<div id="list-view-container" style="display: none;">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Tanggal</th>
                    <th>Nama Pelanggan</th>
                    <th>Layanan</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($events)): ?>
                    <?php $counter = 1; ?>
                    <?php foreach($events as $event): ?>
                        <?php
                        // Status badge dengan pengecekan isset
                        $status = $event['status'] ?? 'pending'; // Default value jika tidak ada
                        $badgeClass = [
                            'pending' => 'bg-warning',
                            'dikonfirmasi' => 'bg-info',
                            'diproses' => 'bg-primary',
                            'selesai' => 'bg-success',
                            'dibatalkan' => 'bg-danger'
                        ][$status] ?? 'bg-secondary';
                        
                        // Service type dengan pengecekan isset
                        $jenisLayanan = $event['jenis_layanan'] ?? 'makeup';
                        $serviceType = [
                            'makeup' => 'Makeup',
                            'kostum' => 'Kostum',
                            'keduanya' => 'Makeup & Kostum'
                        ][$jenisLayanan] ?? $jenisLayanan;
                        
                        // Data dengan pengecekan isset
                        $namaLengkap = $event['nama_lengkap'] ?? 'Tidak diketahui';
                        $noWhatsapp = $event['no_whatsapp'] ?? '';
                        $lokasi = $event['lokasi_acara'] ?? 'Belum ditentukan';
                        $kodePesanan = $event['kode_pesanan'] ?? '';
                        $eventId = $event['id'] ?? 0;
                        $tanggalAcara = $event['tanggal_acara'] ?? date('Y-m-d');
                        ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($tanggalAcara)) ?></strong><br>
                                <small class="text-muted"><?= date('l', strtotime($tanggalAcara)) ?></small>
                            </td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($namaLengkap) ?></div>
                                <?php if($noWhatsapp): ?>
                                <small class="text-muted"><?= htmlspecialchars($noWhatsapp) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= $serviceType ?></td>
                            <td><?= htmlspecialchars($lokasi) ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if($eventId): ?>
                                    <a href="<?= base_url('admin/pesanan/detail/' . $eventId) ?>" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if($noWhatsapp): ?>
                                    <a href="https://wa.me/<?= $noWhatsapp ?>" 
                                       target="_blank" class="btn btn-outline-success" title="WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Tidak ada acara pada periode ini</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
                </div>
            </div>
        </div>
        
        <!-- Statistik & Ringkasan -->
        <div class="col-lg-4 mb-4">
            <!-- Legenda Status -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-palette"></i> Legenda Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2" style="width: 15px; height: 15px; background-color: #ffc107;"></div>
                            <span>Pending</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2" style="width: 15px; height: 15px; background-color: #17a2b8;"></div>
                            <span>Dikonfirmasi</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2" style="width: 15px; height: 15px; background-color: #007bff;"></div>
                            <span>Diproses</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2" style="width: 15px; height: 15px; background-color: #28a745;"></div>
                            <span>Selesai</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle me-2" style="width: 15px; height: 15px; background-color: #dc3545;"></div>
                            <span>Dibatalkan</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Harian -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="bi bi-calendar-check"></i> Ringkasan Harian</h6>
    </div>
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            <?php if(!empty($monthlySummary) && is_array($monthlySummary)): ?>
                <?php 
                // Sort by date
                ksort($monthlySummary);
                $count = 0;
                ?>
                <?php foreach($monthlySummary as $date => $summary): ?>
                    <?php 
                    // Pastikan $summary adalah array dan memiliki key yang diperlukan
                    if(is_array($summary) && isset($summary['total_events']) && $count < 7): 
                    ?>
                        <a href="javascript:void(0)" onclick="showDateEvents('<?= $date ?>')" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold"><?= date('d M', strtotime($date)) ?></div>
                                <small class="text-muted"><?= date('l', strtotime($date)) ?></small>
                            </div>
                            <div>
                                <span class="badge bg-primary rounded-pill">
                                    <?= $summary['total_events'] ?> acara
                                </span>
                            </div>
                        </a>
                        <?php $count++; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if($count === 0): ?>
                    <div class="text-center py-3">
                        <small class="text-muted">Tidak ada acara pada periode ini</small>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-3">
                    <small class="text-muted">Tidak ada data ringkasan</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
            
            <!-- Informasi -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <i class="bi bi-calendar-day"></i> Klik tanggal untuk melihat daftar acara<br>
                        <i class="bi bi-calendar-event"></i> Klik acara untuk melihat detail<br>
                        <i class="bi bi-whatsapp"></i> Tombol hijau untuk kirim WhatsApp<br>
                        <i class="bi bi-eye"></i> Tombol biru untuk melihat detail pesanan
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar Acara per Tanggal -->
<div class="modal fade" id="dateEventsModal" tabindex="-1" aria-labelledby="dateEventsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateEventsModalLabel">Daftar Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="selected-date-title"></h6>
                    <p class="text-muted" id="selected-date-info"></p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="date-events-table">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th>Nama</th>
                                <th>Layanan</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="date-events-body">
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Acara -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailModalLabel">Detail Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="event-detail-content">
                <!-- Konten akan dimuat via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-primary" id="btn-edit-event">Detail Pesanan</a>
                <a href="#" class="btn btn-success" id="btn-whatsapp" target="_blank">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- CSS Custom untuk Kalender -->
<style>
.fc {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.fc .fc-toolbar-title {
    font-size: 1.3rem;
    font-weight: 600;
}

.fc .fc-button {
    background-color: #6c757d;
    border-color: #6c757d;
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: #495057;
    border-color: #495057;
}

.fc .fc-daygrid-day-number {
    font-weight: 500;
    color: #495057;
}

.fc .fc-col-header-cell-cushion {
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.fc-daygrid-dot-event {
    cursor: pointer;
}

.fc-event {
    border: none;
    border-radius: 3px;
    padding: 1px 3px;
    margin: 1px 0;
    cursor: pointer;
    font-size: 0.75rem;
    transition: transform 0.2s;
}

.fc-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.fc-daygrid-day-events {
    min-height: 15px;
}

.fc-daygrid-event-harness {
    margin: 1px 0;
}

.fc-daygrid-day-top {
    position: relative;
}

.event-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    z-index: 1;
}

.date-with-events {
    position: relative;
}

.date-with-events .fc-daygrid-day-number {
    font-weight: bold;
    color: #dc3545;
}

/* Highlight current month */
.fc-daygrid-day.fc-day-today {
    background-color: rgba(212, 184, 163, 0.1) !important;
}

/* Style untuk hari yang ada acara */
.fc-daygrid-day.fc-day-today.date-with-events .fc-daygrid-day-number {
    color: #28a745 !important;
    font-weight: bold;
}

/* Responsive calendar */
@media (max-width: 768px) {
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1rem;
    }
    
    .fc .fc-button {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .fc-event {
        font-size: 0.7rem;
        padding: 1px 2px;
    }
}
</style>

<!-- Script untuk FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>

<script>
$(document).ready(function() {
    // Inisialisasi calendar
    let calendar;
    
    initializeCalendar();
    
    // Toggle between calendar and list view
    $('#toggle-calendar-view').on('change', function() {
        if ($(this).is(':checked')) {
            $('#calendar-container').hide();
            $('#list-view-container').show();
        } else {
            $('#calendar-container').show();
            $('#list-view-container').hide();
        }
    });
    
    // Auto submit form when month/year changes
    $('#month-select, #year-select').on('change', function() {
        $('#month-year-form').submit();
    });
});

// Inisialisasi FullCalendar dengan bulan & tahun terpilih
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    
    // Set initial date to selected month and year
    const initialDate = new Date(<?= $selectedYear ?>, <?= $selectedMonth - 1 ?>, 1);
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'id',
        timeZone: 'Asia/Jakarta',
        initialDate: initialDate,
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'title',
            center: '',
            right: 'prev,next today'
        },
        themeSystem: 'bootstrap5',
        editable: false,
        selectable: false,
        dayMaxEvents: 4,
        weekends: true,
        navLinks: false,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            $.ajax({
                url: '<?= base_url("admin/calendar/getEvents") ?>',
                type: 'GET',
                data: {
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr,
                    status: '' // No status filter for now
                },
                dataType: 'json',
                success: function(events) {
                    successCallback(events);
                    // Update badge pada tanggal
                    updateDateBadges(fetchInfo.start, fetchInfo.end);
                },
                error: function() {
                    failureCallback('Gagal memuat data acara');
                }
            });
        },
        eventClick: function(info) {
            showEventDetail(info.event);
        },
        dateClick: function(info) {
            showDateEvents(info.dateStr);
        },
        eventDidMount: function(info) {
            // Tooltip untuk event
            $(info.el).tooltip({
                title: info.event.extendedProps.details,
                html: true,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        dayCellDidMount: function(info) {
            // Tambah badge untuk tanggal yang ada acara
            const dateStr = info.date.toISOString().split('T')[0];
            
            // Cek apakah ada event di tanggal ini
            const events = info.view.calendar.getEvents();
            const hasEvents = events.some(event => {
                const eventDate = event.start.toISOString().split('T')[0];
                return eventDate === dateStr;
            });
            
            if (hasEvents) {
                $(info.el).addClass('date-with-events');
                
                // Hitung jumlah event
                const eventCount = events.filter(event => {
                    const eventDate = event.start.toISOString().split('T')[0];
                    return eventDate === dateStr;
                }).length;
                
                // Tambah badge jumlah jika lebih dari 1
                if (eventCount > 1) {
                    const badge = document.createElement('div');
                    badge.className = 'event-badge bg-danger';
                    badge.style.cssText = 'width: 16px; height: 16px; font-size: 9px; color: white; text-align: center; line-height: 16px; border-radius: 50%; position: absolute; top: 2px; right: 2px;';
                    badge.textContent = eventCount;
                    info.el.appendChild(badge);
                }
            }
            
            // Highlight dates with events from monthly summary
            const monthlySummary = <?= json_encode($monthlySummary) ?>;
            if (monthlySummary[dateStr]) {
                $(info.el).addClass('date-with-events');
                
                // Add small dot indicator
                if (!info.el.querySelector('.event-badge')) {
                    const dot = document.createElement('div');
                    dot.className = 'event-badge bg-primary';
                    dot.style.cssText = 'width: 6px; height: 6px; border-radius: 50%; position: absolute; top: 4px; right: 4px;';
                    info.el.appendChild(dot);
                }
            }
        },
        datesSet: function(info) {
            // Update form with current view month/year
            const currentDate = info.view.currentStart;
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();
            
            // Only update if different from selected
            if (currentMonth != <?= $selectedMonth ?> || currentYear != <?= $selectedYear ?>) {
                // Update URL with new month/year
                window.history.replaceState({}, '', 
                    '<?= base_url("admin/calendar") ?>?year=' + currentYear + '&month=' + currentMonth);
                
                // Update form values
                $('#month-select').val(currentMonth);
                $('#year-select').val(currentYear);
            }
        }
    });
    
    calendar.render();
}

// Update badge pada tanggal
function updateDateBadges(start, end) {
    // Kosongkan dulu semua badge
    $('.event-badge').remove();
    $('.date-with-events').removeClass('date-with-events');
    
    // Ambil event untuk periode ini
    const events = calendar.getEvents();
    const eventDates = {};
    
    // Group event by date
    events.forEach(event => {
        if (event.start) {
            const dateStr = event.start.toISOString().split('T')[0];
            if (!eventDates[dateStr]) {
                eventDates[dateStr] = [];
            }
            eventDates[dateStr].push(event);
        }
    });
    
    // Update badge untuk setiap tanggal
    Object.keys(eventDates).forEach(dateStr => {
        const dateCell = document.querySelector(`[data-date="${dateStr}"]`);
        if (dateCell) {
            $(dateCell).addClass('date-with-events');
            
            const eventCount = eventDates[dateStr].length;
            if (eventCount > 1) {
                const badge = document.createElement('div');
                badge.className = 'event-badge bg-danger';
                badge.style.cssText = 'width: 16px; height: 16px; font-size: 9px; color: white; text-align: center; line-height: 16px; border-radius: 50%; position: absolute; top: 2px; right: 2px;';
                badge.textContent = eventCount;
                dateCell.appendChild(badge);
            } else {
                // Add small dot for single event
                const dot = document.createElement('div');
                dot.className = 'event-badge bg-primary';
                dot.style.cssText = 'width: 6px; height: 6px; border-radius: 50%; position: absolute; top: 4px; right: 4px;';
                dateCell.appendChild(dot);
            }
        }
    });
    
    // Also check monthly summary for dates without events but with data
    <?php if(isset($monthlySummary) && is_array($monthlySummary)): ?>
    const monthlySummary = <?= json_encode($monthlySummary) ?>;
    if (monthlySummary) {
        Object.keys(monthlySummary).forEach(dateStr => {
            if (!eventDates[dateStr]) {
                const dateCell = document.querySelector(`[data-date="${dateStr}"]`);
                if (dateCell) {
                    $(dateCell).addClass('date-with-events');
                    
                    // Add indicator dot
                    if (!dateCell.querySelector('.event-badge')) {
                        const dot = document.createElement('div');
                        dot.className = 'event-badge bg-info';
                        dot.style.cssText = 'width: 6px; height: 6px; border-radius: 50%; position: absolute; top: 4px; right: 4px;';
                        dateCell.appendChild(dot);
                    }
                }
            }
        });
    }
    <?php endif; ?>
}

// Tampilkan detail event
function showEventDetail(event) {
    const eventId = event.id;
    const eventTitle = event.title;
    const eventDate = event.start.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    // Load event detail via AJAX
    $.ajax({
        url: '<?= base_url("admin/pesanan/getDetail") ?>/' + eventId,
        type: 'GET',
        beforeSend: function() {
            $('#event-detail-content').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);
        },
        success: function(response) {
            $('#event-detail-content').html(response);
            $('#eventDetailModalLabel').text('Detail Acara - ' + eventTitle);
            $('#btn-edit-event').attr('href', '<?= base_url("admin/pesanan/detail/") ?>' + eventId);
            
            // Set WhatsApp button
            const phone = event.extendedProps.phone;
            if (phone) {
                const whatsappUrl = `https://wa.me/${phone}?text=Halo%20${encodeURIComponent(event.extendedProps.customer)}%2C%20saya%20dari%20Maulia%20Wedding.%20Mengenai%20acara%20Anda%20tanggal%20${encodeURIComponent(eventDate)}%20...`;
                $('#btn-whatsapp').attr('href', whatsappUrl);
            }
            
            $('#eventDetailModal').modal('show');
        },
        error: function() {
            $('#event-detail-content').html(`
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-2">Gagal memuat data acara</p>
                </div>
            `);
            $('#eventDetailModal').modal('show');
        }
    });
}

// Tampilkan daftar acara pada tanggal tertentu
function showDateEvents(dateStr) {
    const dateObj = new Date(dateStr);
    const formattedDate = dateObj.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    $('#selected-date-title').text('Acara pada ' + formattedDate);
    $('#selected-date-info').text('Daftar semua acara yang terjadwal pada tanggal ini');
    
    // Load events for this date
    $.ajax({
        url: '<?= base_url("admin/calendar/getEventsByDate") ?>?date=' + dateStr,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#date-events-body').html(`
                <tr>
                    <td colspan="6" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        <span class="ms-2">Memuat acara...</span>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (response.success && response.events.length > 0) {
                let html = '';
                response.events.forEach(function(event, index) {
                    // Status badge
                    const badgeClass = {
                        'pending': 'bg-warning',
                        'dikonfirmasi': 'bg-info',
                        'diproses': 'bg-primary',
                        'selesai': 'bg-success',
                        'dibatalkan': 'bg-danger'
                    }[event.status] || 'bg-secondary';
                    
                    // Service type
                    const serviceType = {
                        'makeup': 'Makeup',
                        'kostum': 'Kostum',
                        'keduanya': 'Makeup & Kostum'
                    }[event.jenis_layanan] || event.jenis_layanan;
                    
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <strong>${event.nama_lengkap}</strong><br>
                                <small class="text-muted">${event.kode_pesanan}</small>
                            </td>
                            <td>${serviceType}</td>
                            <td>${event.lokasi_acara}</td>
                            <td>
                                <span class="badge ${badgeClass}">
                                    ${event.status.charAt(0).toUpperCase() + event.status.slice(1)}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('admin/pesanan/detail/') ?>${event.id}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="https://wa.me/${event.no_whatsapp}" 
                                       target="_blank" class="btn btn-outline-success" title="WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                $('#date-events-body').html(html);
            } else {
                $('#date-events-body').html(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Tidak ada acara pada tanggal ini</p>
                        </td>
                    </tr>
                `);
            }
            
            $('#dateEventsModal').modal('show');
        },
        error: function() {
            $('#date-events-body').html(`
                <tr>
                    <td colspan="6" class="text-center text-danger py-3">
                        <i class="bi bi-exclamation-triangle"></i>
                        Gagal memuat data acara
                    </td>
                </tr>
            `);
            $('#dateEventsModal').modal('show');
        }
    });
}

// Print calendar view
function printCalendar() {
    const printContent = document.getElementById('calendar').cloneNode(true);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Kalender Acara - Maulia Wedding</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    body { font-size: 11pt; margin: 0; }
                    .fc { margin: 0 !important; }
                    .fc .fc-toolbar-title { font-size: 1.2rem; }
                    .fc-event { font-size: 9pt; padding: 1px 2px; }
                    .no-print { display: none !important; }
                }
                .fc { margin-top: 10px; }
                .print-header { 
                    text-align: center; 
                    margin-bottom: 10px;
                    border-bottom: 2px solid #d4b8a3;
                    padding-bottom: 5px;
                }
                .print-date { 
                    text-align: right; 
                    font-size: 0.9rem;
                    color: #666;
                    margin-bottom: 10px;
                }
                .print-info {
                    font-size: 0.8rem;
                    color: #666;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                <div class="print-header">
                    <h4>Kalender Acara Maulia Wedding</h4>
                    <p>Periode: <?= $months[$selectedMonth] ?> <?= $selectedYear ?></p>
                </div>
                <div class="print-date">
                    Dicetak: ${new Date().toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                </div>
                <div id="print-calendar"></div>
                <div class="print-info">
                    <p><strong>Informasi:</strong></p>
                    <p>• Warna merah: Dibatalkan</p>
                    <p>• Warna hijau: Selesai</p>
                    <p>• Warna biru: Diproses</p>
                    <p>• Warna biru muda: Dikonfirmasi</p>
                    <p>• Warna kuning: Pending</p>
                </div>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.getElementById('print-calendar').appendChild(printContent);
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}

// Export calendar as image (optional)
function exportCalendarAsImage() {
    html2canvas(document.querySelector("#calendar"), {
        backgroundColor: '#ffffff',
        scale: 2
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'kalender-<?= $selectedMonth ?>-<?= $selectedYear ?>.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}
</script>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<?= $this->endSection() ?>