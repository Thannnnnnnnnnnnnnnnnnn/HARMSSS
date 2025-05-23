<?php require 'partials/admin/head.php' ?>

<div class="flex min-h-screen w-full">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <?php require 'partials/admin/sidebar.php' ?>

    <div class="main w-full bg-[#FFF6E8] md:ml-[320px]">
        <?php require 'partials/admin/navbar.php' ?>
        <main class="px-8 py-8">
            <div class="pb-5 text-lg font-semibold tracking-wide text-[#594423]">
                <h1>Department Audit Trail</h1>
            </div>
            <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
                <table class="table text-center">
                    <!-- head -->
                    <thead class="text-[#594423]">
                        <tr>
                            <th>Log ID</th>
                            <th>module</th>
                            <th>Department</th>
                            <th>Module</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- row 1 -->
                        <?php foreach ($audit as $row): ?>
                            <tr>
                                <th><?= $row['dept_audit_trail_id'] ?></th>
                                <td><?= $row['action'] ?></td>
                                <td><?= $row['department_affected'] ?></td>
                                <td><?= $row['module_affected'] ?></td>
                                <td><i class="fa-solid fa-eye"></i></td>
                            </tr>
                        <?php endforeach ?>
                        <?php foreach ($pm as $pm_audit): ?>
                            <tr>
                                <th><?= $pm_audit['User_Audit_Trail_ID'] ?></th>
                                <td><?= $pm_audit['Action'] ?></td>
                                <td><?= $pm_audit['Department_Affected'] ?></td>
                                <td><?= $pm_audit['Module_Affected'] ?></td>
                                <td><i class="fa-solid fa-eye"></i></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<?php require 'partials/admin/footer.php' ?>