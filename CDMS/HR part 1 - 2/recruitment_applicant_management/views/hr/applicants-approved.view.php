<?php require 'partials/head.php' ?>
<?php require 'partials/hr/navbar.php' ?>

<main class="max-w-7xl mx-auto mt-6 p-6 flex-grow">
    <?php require 'partials/hr/nav.php' ?>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table max-w-7xl">
            <thead>
                <tr class="bg-[#594423] text-white">
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Resume</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applicants as $applicant) : ?>
                    <tr class="hover:bg-gray-100">
                        <td class="applicant_id"><?= htmlspecialchars($applicant['applicant_id']) ?></td>
                        <td><?= htmlspecialchars($applicant['first_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['last_name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['contact_number']) ?></td>
                        <td><?= htmlspecialchars($applicant['address']) ?></td>
                        <td>
                            <?php if ($applicant['resume']) : ?>
                                <a href="../<?= htmlspecialchars($applicant['resume']) ?>" class="text-[#594423] hover:underline" target="_blank">View Resume</a>
                            <?php else : ?>
                                <p class="text-lg text-red-500">No Resume</p>
                            <?php endif ?>
                        </td>
                        <td>
                            <a href="/hr/set-interview?id=<?= htmlspecialchars($applicant['applicant_id']) ?>" class="btn hover:bg-[#594423] hover:text-white rounded-lg">Set interview</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?php if (count($applicants) < 1) : ?>
        <div role="alert" class="alert alert-error mx-20 mt-5">
            <box-icon name='x-circle'></box-icon>
            <span>No Data Found.</span>
        </div>
    <?php endif ?>
</main>

<script>
    document.querySelectorAll('.openModal').forEach(button => {
        button.addEventListener('click', function() {
            const row = event.target.closest('tr');
            const applicantId = row.querySelector('.applicant_id').textContent;
            const id_input = document.getElementById('applicant_id').value = applicantId;
        });
    });
    $('#interview').on('click', function() {
        $('input[required]').each(function() {
            var isValid = true;
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('border-red-500');
            } else {
                $(this).removeClass('border-red-500');
            }
            if (isValid) {
                swal.fire({
                    title: 'Interview Set!',
                    text: 'Interview Set succelsfully!',
                    icon: 'success',
                });
                $('#interview-form').submit();
            } else {
                swal.fire({
                    title: 'Error!',
                    text: 'Please fill all the fields.',
                    icon: 'error',
                });
            }
        });
    });
</script>

<?php require 'partials/footer.php' ?>