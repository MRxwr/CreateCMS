<?php
$leads = selectDB("leads", "status = '1' ORDER BY id DESC");
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 text-primary">Leads Management</h1>
                    <p class="text-muted">Manage your potential customers and prospects</p>
                </div>
                <button class="btn btn-primary btn-lg" onclick="showAddModal('lead')">
                    <i class="bi bi-plus-circle"></i> Add New Lead
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchLeads" placeholder="Search leads...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterCompany">
                                <option value="">All Companies</option>
                                <!-- Companies will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="sortBy">
                                <option value="name">Sort by Name</option>
                                <option value="company">Sort by Company</option>
                                <option value="date">Sort by Date</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="exportLeads()">
                                <i class="bi bi-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2><?php echo count($leads ?: []); ?></h2>
                    <p class="mb-0">Total Leads</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people"></i> All Leads
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($leads && is_array($leads)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="leadsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Company</th>
                                        <th>Added Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($leads as $lead): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    <?php echo strtoupper(substr($lead['name'], 0, 2)); ?>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($lead['name']); ?></strong>
                                                    <?php if($lead['notes']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($lead['notes'], 0, 50)); ?>...</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo $lead['email']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($lead['email']); ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if($lead['phone']): ?>
                                            <a href="tel:<?php echo $lead['phone']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($lead['phone']); ?>
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">No phone</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $lead['company'] ? htmlspecialchars($lead['company']) : '<span class="text-muted">No company</span>'; ?>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($lead['created_at'] ?? $lead['date'] ?? 'now')); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" onclick="viewLead(<?php echo $lead['id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" onclick="editLead(<?php echo $lead['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('lead', <?php echo $lead['id']; ?>, '<?php echo addslashes($lead['name']); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-person-plus display-1 text-muted"></i>
                            <h3 class="text-muted">No Leads Found</h3>
                            <p class="text-muted">Start building your customer base by adding your first lead.</p>
                            <button class="btn btn-primary btn-lg" onclick="showAddModal('lead')">
                                <i class="bi bi-plus-circle"></i> Add Your First Lead
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
// Search functionality
document.getElementById('searchLeads').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#leadsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Sort functionality
document.getElementById('sortBy').addEventListener('change', function(e) {
    const sortBy = e.target.value;
    const tbody = document.querySelector('#leadsTable tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortBy) {
            case 'name':
                aValue = a.cells[0].textContent.trim();
                bValue = b.cells[0].textContent.trim();
                break;
            case 'company':
                aValue = a.cells[3].textContent.trim();
                bValue = b.cells[3].textContent.trim();
                break;
            case 'date':
                aValue = a.cells[4].textContent.trim();
                bValue = b.cells[4].textContent.trim();
                break;
        }
        
        return aValue.localeCompare(bValue);
    });
    
    rows.forEach(row => tbody.appendChild(row));
});

// View lead details
function viewLead(id) {
    fetch(`requests/apiLeads.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const lead = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ${lead.name}</p>
                            <p><strong>Email:</strong> ${lead.email}</p>
                            <p><strong>Phone:</strong> ${lead.phone || 'Not provided'}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Company:</strong> ${lead.company || 'Not provided'}</p>
                            <p><strong>Added:</strong> ${new Date(lead.created_at || lead.date).toLocaleDateString()}</p>
                        </div>
                        <div class="col-12">
                            <p><strong>Notes:</strong></p>
                            <p>${lead.notes || 'No notes available'}</p>
                        </div>
                    </div>
                `;
                
                const modal = createModal('viewLeadModal', `Lead Details - ${lead.name}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading lead details', 'danger'));
}

// Edit lead
function editLead(id) {
    fetch(`requests/apiLeads.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                const lead = data.data;
                const content = getLeadForm(lead) + `<input type="hidden" name="id" value="${id}">`;
                const modal = createModal('editLeadModal', `Edit Lead - ${lead.name}`, content);
                modal.show();
            }
        })
        .catch(error => showToast('Error loading lead details', 'danger'));
}

// Export leads
function exportLeads() {
    window.open('requests/apiLeads.php?export=csv', '_blank');
}
</script>
