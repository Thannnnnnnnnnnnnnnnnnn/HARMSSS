console.log("collection.js loaded");

const addPaymentBtn = document.getElementById("add-payment-btn");
const createModal = document.getElementById("create-payment-modal");
const closeCreateBtn = document.getElementById("close-create-btn");
const viewModal = document.getElementById("viewPaymentModal");
const closeViewBtn = document.getElementById("close-view-btn");
const editModal = document.getElementById("editPaymentModal");
const closeEditBtn = document.getElementById("close-edit-btn");
const settlementModal = document.getElementById("settlementModal");
const closeSettlementBtn = document.getElementById("close-settlement-btn");
const generateInvoiceBtn = document.getElementById("generate-invoice-btn");
const generateInvoiceModal = document.getElementById("generate-invoice-modal");
const closeInvoiceBtn = document.getElementById("close-invoice-btn");
const viewReservationsBtn = document.getElementById("view-reservations-btn");
const viewReservationsModal = document.getElementById(
  "view-reservations-modal"
);
const closeReservationsBtn = document.getElementById("close-reservations-btn");
const verifyReservationModal = document.getElementById(
  "verify-reservation-modal"
);
const verifyYesBtn = document.getElementById("verify-yes-btn");
const verifyNoBtn = document.getElementById("verify-no-btn");

addPaymentBtn.addEventListener("click", () => {
  createModal.classList.remove("hidden");
  flatpickr("#createStartDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    altInput: true,
    altFormat: "F j, Y h:i K",
    onOpen: function () {
      document.querySelector(".flatpickr-calendar").style.backgroundColor =
        "#FFF6E8";
      document.querySelector(".flatpickr-calendar").style.border =
        "1px solid #d1d5db";
      document.querySelector(".flatpickr-calendar").style.boxShadow =
        "0 4px 6px rgba(0, 0, 0, 0.1)";
    },
  });
  flatpickr("#createEndDate", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    altInput: true,
    altFormat: "F j, Y h:i K",
    onOpen: function () {
      document.querySelector(".flatpickr-calendar").style.backgroundColor =
        "#FFF6E8";
      document.querySelector(".flatpickr-calendar").style.border =
        "1px solid #d1d5db";
      document.querySelector(".flatpickr-calendar").style.boxShadow =
        "0 4px 6px rgba(0, 0, 0, 0.1)";
    },
  });

  $("#createStartDateTrigger").on("click", function () {
    $("#createStartDate").click();
  });
  $("#createEndDateTrigger").on("click", function () {
    $("#createEndDate").click();
  });
});
closeCreateBtn.addEventListener("click", () =>
  createModal.classList.add("hidden")
);
closeViewBtn.addEventListener("click", () => viewModal.classList.add("hidden"));
closeEditBtn.addEventListener("click", () => editModal.classList.add("hidden"));
closeSettlementBtn.addEventListener("click", () =>
  settlementModal.classList.add("hidden")
);
generateInvoiceBtn.addEventListener("click", () =>
  generateInvoiceModal.classList.remove("hidden")
);
closeInvoiceBtn.addEventListener("click", () =>
  generateInvoiceModal.classList.add("hidden")
);

viewReservationsBtn.addEventListener("click", () => {
  viewReservationsModal.classList.remove("hidden");
});

closeReservationsBtn.addEventListener("click", () =>
  viewReservationsModal.classList.add("hidden")
);
verifyNoBtn.addEventListener("click", () =>
  verifyReservationModal.classList.add("hidden")
);

function attachPreviewModalListeners() {
  const invoicePreviewModal = document.getElementById("invoice-preview-modal");
  const printInvoiceBtn = document.getElementById("print-invoice-btn");
  const sendInvoiceBtn = document.getElementById("send-invoice-btn");
  const closePreviewBtn = document.getElementById("close-preview-btn");

  if (invoicePreviewModal) {
    closePreviewBtn.addEventListener("click", () =>
      invoicePreviewModal.classList.add("hidden")
    );
    printInvoiceBtn.addEventListener("click", () => {
      const pdfFrame = invoicePreviewModal.querySelector("embed");
      pdfFrame.contentWindow.print();
    });
    sendInvoiceBtn.addEventListener("click", () => {
      alert("Kalma. Maghintay ka lang");
    });
  }
}

const miniModal = document.getElementById("mini-modal");
const invoiceInput = document.getElementById("invoiceID");
const generateButton = document.getElementById("generate-invoice-submit");
let isValid = false;

invoiceInput.addEventListener("input", function (e) {
  const invoiceID = this.value.trim();
  if (invoiceID === "") {
    miniModal.style.display = "none";
    generateButton.disabled = true;
    generateButton.classList.add("cursor-not-allowed", "opacity-50");
    generateButton.classList.remove("hover:bg-blue-700");
    isValid = false;
    return;
  }

  fetch("./includes/Validation.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "validate_invoice_id=1&invoice_id=" + encodeURIComponent(invoiceID),
  })
    .then((response) => response.json())
    .then((data) => {
      miniModal.style.left = e.pageX + 10 + "px";
      miniModal.style.top = e.pageY + 10 + "px";
      if (data.status === "success") {
        miniModal.textContent = `Guest: ${data.guest_name}`;
        miniModal.className = "success";
        generateButton.disabled = false;
        generateButton.classList.remove("cursor-not-allowed", "opacity-50");
        generateButton.classList.add("hover:bg-blue-700");
        isValid = true;
      } else {
        miniModal.textContent = data.message;
        miniModal.className = "error";
        generateButton.disabled = true;
        generateButton.classList.add("cursor-not-allowed", "opacity-50");
        generateButton.classList.remove("hover:bg-blue-700");
        isValid = false;
      }
      miniModal.style.display = "block";
    })
    .catch((error) => {
      console.error("Validation error:", error);
      miniModal.textContent = "Error checking Invoice ID";
      miniModal.className = "error";
      miniModal.style.display = "block";
      generateButton.disabled = true;
      generateButton.classList.add("cursor-not-allowed", "opacity-50");
      generateButton.classList.remove("hover:bg-blue-700");
      isValid = false;
    });
});

document.addEventListener("mousemove", function (e) {
  if (miniModal.style.display === "block") {
    miniModal.style.left = e.pageX + 10 + "px";
    miniModal.style.top = e.pageY + 10 + "px";
  }
});

invoiceInput.addEventListener("blur", () => {
  setTimeout(() => (miniModal.style.display = "none"), 200);
});

document
  .getElementById("invoice-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    if (!isValid) {
      miniModal.textContent = "Please enter a valid Invoice ID";
      miniModal.className = "error";
      miniModal.style.display = "block";
      setTimeout(() => (miniModal.style.display = "none"), 2000);
      return;
    }

    const invoiceID = invoiceInput.value.trim();

    generateButton.disabled = true;
    generateButton.innerHTML =
      '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Generating...';

    fetch("./includes/InvoiceHandler.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "generate_invoice=true&invoiceID=" + encodeURIComponent(invoiceID),
    })
      .then((response) => response.text())
      .then((data) => {
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = data;

        const previewModal = tempDiv.querySelector("#invoice-preview-modal");
        if (previewModal) {
          const existingModal = document.getElementById(
            "invoice-preview-modal"
          );
          if (existingModal) existingModal.remove();

          document.body.appendChild(previewModal);
          previewModal.classList.remove("hidden");
          generateInvoiceModal.classList.add("hidden");
          attachPreviewModalListeners();
        } else {
          Swal.fire({
            title: "Error!",
            text: "Failed to generate invoice preview",
            icon: "error",
          });
        }
      })
      .catch((error) => {
        console.error("Invoice generation error:", error);
        Swal.fire({
          title: "Error!",
          text: "An error occurred while generating the invoice",
          icon: "error",
        });
      })
      .finally(() => {
        generateButton.disabled = false;
        generateButton.innerHTML =
          '<i class="fa-solid fa-eye mr-2"></i>Preview';
      });
  });

$(document).ready(function () {
  console.log("jQuery ready");
  console.log("SweetAlert2 available:", typeof Swal !== "undefined");

  $(document).on("submit", "#createPaymentForm", function (e) {
    e.preventDefault();
    const guestName = $("#createGuestName").val();
    const totalAmount = parseFloat($("#createTotalAmount").val());
    const amountPay = parseFloat($("#createAmountPay").val());
    const startDate = new Date($("#createStartDate").val());
    const endDate = new Date($("#createEndDate").val());
    const paymentType = $("#createPaymentType").val();
    const today = new Date();
    const startDateOnly = startDate.toISOString().split("T")[0];
    const todayDate = today.toISOString().split("T")[0];
    const status =
      startDateOnly === todayDate
        ? amountPay < totalAmount
          ? "Downpayment"
          : "Fully Paid"
        : startDate > today
        ? "Reservation"
        : amountPay < totalAmount
        ? "Downpayment"
        : "Fully Paid";

    if (amountPay <= 0) {
      Swal.fire("Error!", "Amount paid must be greater than 0.", "error");
      return;
    }
    if (totalAmount <= 0) {
      Swal.fire("Error!", "Total amount must be greater than 0.", "error");
      return;
    }
    if (endDate - startDate < 0) {
      Swal.fire("Error!", "Check-Out cannot be before Check-In.", "error");
      return;
    }

    $.ajax({
      url: "./includes/CreatePayment.php",
      type: "POST",
      data:
        $(this).serialize() + "&create=1&status=" + encodeURIComponent(status),
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            title: "Success!",
            text: "Payment has been created successfully.",
            icon: "success",
            confirmButtonColor: "#22c55e",
          }).then(() => {
            $("#createPaymentModal").addClass("hidden");
            if (response.data.Status === "Reservation") {
              const badge = document.getElementById("reservation-count");
              let currentCount = badge ? parseInt(badge.textContent) : 0;
              currentCount++;
              if (badge) {
                badge.textContent = currentCount;
                badge.classList.add("notification-unseen");
              } else {
                $("#view-reservations-btn").append(
                  `<span id="reservation-count" class="notification-badge notification-unseen">${currentCount}</span>`
                );
              }
            }
            location.reload();
          });
        } else {
          Swal.fire(
            "Error!",
            response.message || "Failed to create payment.",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to create payment: " + error, "error");
      },
    });
  });

  $(document).on("click", ".ViewInfo", function (e) {
    e.stopPropagation();
    let paymentID = $(this).data("id");
    $.ajax({
      url: "./includes/ViewPayment.php",
      type: "POST",
      data: { paymentID: paymentID },
      success: function (response) {
        let tempDiv = $("<div>").html(response);
        let statusElement = tempDiv.find(".status-value");
        let totalAmount = parseFloat(
          tempDiv
            .find(".total-amount")
            .text()
            .replace("₱", "")
            .replace(/,/g, "")
        );
        let amountPay = parseFloat(
          tempDiv.find(".amount-paid").text().replace("₱", "").replace(/,/g, "")
        );
        if (
          statusElement.text().trim() !== "Settled" &&
          statusElement.text().trim() !== "Reservation"
        ) {
          let calculatedStatus =
            amountPay < totalAmount ? "Downpayment" : "Fully Paid";
          statusElement.text(calculatedStatus);
        }
        $(".modal-body-view").html(tempDiv.html());
        $("#viewPaymentModal").removeClass("hidden");
      },
      error: function (xhr, status, error) {
        console.error("View AJAX Error:", status, error);
        Swal.fire(
          "Error!",
          "Failed to load payment details: " + error,
          "error"
        );
      },
    });
  });

  $(document).on("click", ".EditInfo", function (e) {
    e.stopPropagation();
    if ($(this).is(":disabled")) return;
    let paymentID = $(this).data("id");
    $.ajax({
      url: "./includes/EditPayment.php",
      type: "POST",
      data: { paymentID: paymentID },
      success: function (response) {
        $(".modal-body-edit").html(response);
        $("#editPaymentModal").removeClass("hidden");

        flatpickr("#editStartDate", {
          enableTime: true,
          dateFormat: "Y-m-d H:i",
          time_24hr: true,
          altInput: true,
          altFormat: "F j, Y h:i K",
          onOpen: function () {
            document.querySelector(
              ".flatpickr-calendar"
            ).style.backgroundColor = "#FFF6E8";
            document.querySelector(".flatpickr-calendar").style.border =
              "1px solid #d1d5db";
            document.querySelector(".flatpickr-calendar").style.boxShadow =
              "0 4px 6px rgba(0, 0, 0, 0.1)";
          },
        });
        flatpickr("#editEndDate", {
          enableTime: true,
          dateFormat: "Y-m-d H:i",
          time_24hr: true,
          altInput: true,
          altFormat: "F j, Y h:i K",
          onOpen: function () {
            document.querySelector(
              ".flatpickr-calendar"
            ).style.backgroundColor = "#FFF6E8";
            document.querySelector(".flatpickr-calendar").style.border =
              "1px solid #d1d5db";
            document.querySelector(".flatpickr-calendar").style.boxShadow =
              "0 4px 6px rgba(0, 0, 0, 0.1)";
          },
        });

        $("#startDateTrigger").on("click", function () {
          $("#editStartDate").click();
        });
        $("#endDateTrigger").on("click", function () {
          $("#editEndDate").click();
        });
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to load edit form: " + error, "error");
      },
    });
  });

  $(document).on("submit", "#editPaymentForm", function (e) {
    e.preventDefault();
    const paymentID = $("#editPaymentID").val();
    const guestName = $("#editGuestName").val();
    const totalAmount = parseFloat($("#editTotalAmount").val());
    const amountPay = parseFloat($("#editAmountPay").val());
    const startDate = new Date($("#editStartDate").val());
    const endDate = new Date($("#editEndDate").val());
    const paymentType = $("#editPaymentType").val();

    if (amountPay <= 0) {
      Swal.fire("Error!", "Amount paid must be greater than 0.", "error");
      return;
    }
    if (totalAmount <= 0) {
      Swal.fire("Error!", "Total amount must be greater than 0.", "error");
      return;
    }
    if (endDate - startDate < 0) {
      Swal.fire("Error!", "Check-Out cannot be before Check-In.", "error");
      return;
    }

    $.ajax({
      url: "./includes/UpdatePayment.php",
      type: "POST",
      data: $(this).serialize() + "&update=1",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            title: "Success!",
            text: "Payment has been updated successfully.",
            icon: "success",
            confirmButtonColor: "#22c55e",
          }).then(() => {
            $("#editPaymentModal").addClass("hidden");
            let $row = $(`#dataRows tr[data-id="${paymentID}"]`);
            let updatedData = response.data || {};
            let status =
              updatedData.Status ||
              (parseFloat(amountPay) < parseFloat(totalAmount)
                ? "Downpayment"
                : "Fully Paid");
            let statusColor =
              status === "Settled"
                ? "gray"
                : status === "Downpayment"
                ? "red"
                : status === "Reservation"
                ? "blue"
                : "green";
            let stayDurationInfo = calculateStayDuration(
              updatedData.StartDate || $row.data("start"),
              updatedData.EndDate || $row.data("end")
            );

            $row.find("td:nth-child(2)").text(guestName);
            $row.find("td:nth-child(3)").text(
              "₱" +
                parseFloat(totalAmount)
                  .toFixed(2)
                  .replace(/\d(?=(\d{3})+\.)/g, "$&,")
            );
            $row.find("td:nth-child(4)").text(
              "₱" +
                parseFloat(amountPay)
                  .toFixed(2)
                  .replace(/\d(?=(\d{3})+\.)/g, "$&,")
            );
            $row
              .find("td:nth-child(5)")
              .text(stayDurationInfo.text)
              .css("color", stayDurationInfo.color);
            $row.find("td:nth-child(6)").text(paymentType);
            $row
              .find("td:nth-child(7)")
              .text(status.replace("FullyPaid", "Fully Paid"))
              .css("color", statusColor);

            $row.data("guest", guestName);
            $row.data("total", totalAmount);
            $row.data("paid", amountPay);
            $row.data("method", paymentType);
            $row.data("status", status);
            $row.data("start", updatedData.StartDate || $row.data("start"));
            $row.data("end", updatedData.EndDate || $row.data("end"));

            if (stayDurationInfo.days === 0 && status !== "Settled") {
              $row
                .addClass("settle-row")
                .removeClass("settled-row reservation-row from-reservation");
            } else {
              $row.removeClass("settle-row");
              if (status === "Settled")
                $row
                  .addClass("settled-row")
                  .removeClass("reservation-row from-reservation");
              else if (status === "Reservation")
                $row
                  .addClass("reservation-row")
                  .removeClass("settled-row from-reservation");
              else
                $row.removeClass(
                  "settled-row reservation-row from-reservation"
                );
            }

            if (status === "Reservation") {
              const badge = document.getElementById("reservation-count");
              let currentCount = badge ? parseInt(badge.textContent) : 0;
              currentCount++;
              if (badge) {
                badge.textContent = currentCount;
                badge.classList.add("notification-unseen");
              } else {
                $("#view-reservations-btn").append(
                  `<span id="reservation-count" class="notification-badge notification-unseen">${currentCount}</span>`
                );
              }
            }
          });
        } else {
          Swal.fire(
            "Error!",
            response.message || "Failed to update payment.",
            "error"
          );
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to update payment: " + error, "error");
      },
    });
  });

  $(document).on("click", ".delete-payment", function (e) {
    e.stopPropagation();
    let paymentID = $(this).data("id");
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "./includes/DeletePayment.php",
          type: "POST",
          data: { paymentID: paymentID },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                title: "Deleted!",
                text: "Payment has been deleted.",
                icon: "success",
              }).then(() => {
                const $row = $(`tr[data-id="${paymentID}"]`);
                if ($row.data("status") === "Reservation") {
                  const badge = document.getElementById("reservation-count");
                  if (badge) {
                    let currentCount = parseInt(badge.textContent);
                    currentCount--;
                    if (currentCount > 0) {
                      badge.textContent = currentCount;
                    } else {
                      badge.classList.remove("notification-unseen");
                      badge.remove();
                    }
                  }
                }
                $row.remove();
              });
            } else {
              Swal.fire("Error!", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            Swal.fire("Error!", "Failed to delete payment: " + error, "error");
          },
        });
      }
    });
  });

  $(document).on(
    "click",
    "#reservationRows tr.unseen-reservation",
    function (e) {
      e.stopPropagation();
      const $row = $(this);
      const paymentID = $row.data("id");
      const isViewed = $row.data("is-viewed");

      if (isViewed == 0) {
        console.log(
          "Attempting to mark reservation as viewed for PaymentID:",
          paymentID
        );
        $.ajax({
          url: "./includes/MarkReservationsViewed.php",
          type: "POST",
          data: { mark_viewed: 1, paymentID: paymentID },
          dataType: "json",
          success: function (response) {
            console.log("Success response:", response);
            if (response.status === "success") {
              // Remove the unseen class to stop blinking and normalize appearance
              $row.removeClass("unseen-reservation");
              $row.data("is-viewed", 1);

              // Ensure row returns to normal styling
              $row.css({
                "background-color": "#ffffff", // Match normal state
                animation: "none", // Stop any animation
              });

              // Update badge count
              const badge = document.getElementById("reservation-count");
              if (badge) {
                let currentCount = parseInt(badge.textContent);
                currentCount--;
                if (currentCount > 0) {
                  badge.textContent = currentCount;
                } else {
                  badge.classList.remove("notification-unseen");
                  badge.remove();
                }
              }
            } else {
              Swal.fire(
                "Error!",
                response.message || "Failed to mark reservation as viewed.",
                "error"
              );
            }
          },
          error: function (xhr, status, error) {
            console.error("AJAX Error:", {
              status: status,
              error: error,
              responseText: xhr.responseText,
              statusCode: xhr.status,
            });
            Swal.fire(
              "Error!",
              "Failed to mark reservation as viewed: " +
                error +
                " (Status: " +
                xhr.status +
                ")",
              "error"
            );
          },
        });
      }
    }
  );

  $(document).on("click", ".edit-reservation", function () {
    let $row = $(this).closest("tr");
    let paymentID = $row.data("id");
    let guestName = $row.data("guest");
    let totalAmount = $row.data("total");
    let amountPay = $row.data("paid");
    let startDate = $row.data("start");
    let endDate = $row.data("end");

    $("#editReservationPaymentID").val(paymentID);
    $("#editReservationGuestName").val(guestName);
    $("#editReservationTotalAmount").val(totalAmount);
    $("#editReservationAmountPay").val(amountPay);
    $("#editReservationStartDate").val(startDate.replace(" ", "T"));
    $("#editReservationEndDate").val(endDate.replace(" ", "T"));

    $("#editReservationModal").removeClass("hidden");
  });

  $("#close-edit-reservation-btn").click(function () {
    $("#editReservationModal").addClass("hidden");
  });

  $("#editReservationForm").on("submit", function (e) {
    e.preventDefault();
    const amountPay = parseFloat($("#editReservationAmountPay").val());
    const totalAmount = parseFloat($("#editReservationTotalAmount").val());
    const startDate = new Date($("#editReservationStartDate").val());
    const endDate = new Date($("#editReservationEndDate").val());

    if (amountPay <= 0) {
      Swal.fire("Error!", "Amount paid must be greater than 0.", "error");
      return;
    }
    if (endDate - startDate < 0) {
      Swal.fire("Error!", "Check-Out cannot be before Check-In.", "error");
      return;
    }

    $.ajax({
      url: "./includes/ReservationHandler.php",
      type: "POST",
      data: $(this).serialize() + "&update_reservation=1",
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            title: "Success!",
            text: response.message,
            icon: "success",
          }).then(() => {
            $("#editReservationModal").addClass("hidden");
            let paymentID = $("#editReservationPaymentID").val();
            let $row = $(`#reservationRows tr[data-id="${paymentID}"]`);
            $row
              .find("td:nth-child(2)")
              .text($("#editReservationGuestName").val());
            $row
              .find("td:nth-child(3)")
              .text(
                "₱" + totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
              );
            $row
              .find("td:nth-child(4)")
              .text(
                "₱" + amountPay.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
              );
            $row
              .find("td:nth-child(5)")
              .text(
                new Date(startDate).toLocaleString("en-US", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                  hour: "2-digit",
                  minute: "2-digit",
                })
              );
            $row
              .find("td:nth-child(6)")
              .text(
                new Date(endDate).toLocaleString("en-US", {
                  year: "numeric",
                  month: "2-digit",
                  day: "2-digit",
                  hour: "2-digit",
                  minute: "2-digit",
                })
              );
            $row.data("guest", $("#editReservationGuestName").val());
            $row.data("total", totalAmount);
            $row.data("paid", amountPay);
            $row.data(
              "start",
              startDate.toISOString().slice(0, 19).replace("T", " ")
            );
            $row.data(
              "end",
              endDate.toISOString().slice(0, 19).replace("T", " ")
            );
          });
        } else {
          Swal.fire("Error!", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to update reservation: " + error, "error");
      },
    });
  });

  $(document).on("click", ".cancel-reservation", function (e) {
    e.stopPropagation();
    let paymentID = $(this).data("id");
    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to cancel this reservation?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, cancel it!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "./includes/ReservationHandler.php",
          type: "POST",
          data: { cancel_reservation: 1, paymentID: paymentID },
          dataType: "json",
          success: function (response) {
            if (response.status === "success") {
              Swal.fire({
                title: "Canceled!",
                text: "Reservation has been canceled.",
                icon: "success",
              }).then(() => {
                const $row = $(`#reservationRows tr[data-id="${paymentID}"]`);
                if ($row.hasClass("unseen-reservation")) {
                  const badge = document.getElementById("reservation-count");
                  if (badge) {
                    let currentCount = parseInt(badge.textContent);
                    currentCount--;
                    if (currentCount > 0) {
                      badge.textContent = currentCount;
                    } else {
                      badge.classList.remove("notification-unseen");
                      badge.remove();
                    }
                  }
                }
                $row.remove();
              });
            } else {
              Swal.fire("Error!", response.message, "error");
            }
          },
          error: function (xhr, status, error) {
            Swal.fire(
              "Error!",
              "Failed to cancel reservation: " + error,
              "error"
            );
          },
        });
      }
    });
  });

  $(document).on("click", ".from-reservation", function () {
    let paymentID = $(this).data("id");
    let guestName = $(this).data("guest");
    $("#verify-guest-name").text(guestName);
    $("#verify-payment-id").val(paymentID);
    $("#verify-reservation-modal").removeClass("hidden");
  });

  verifyYesBtn.addEventListener("click", function () {
    let paymentID = $("#verify-payment-id").val();
    $.ajax({
      url: "./includes/ReservationHandler.php",
      type: "POST",
      data: { verify_reservation: 1, paymentID: paymentID },
      dataType: "json",
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            title: "Verified!",
            text: "Guest arrival verified successfully.",
            icon: "success",
          }).then(() => {
            verifyReservationModal.classList.add("hidden");
            let $row = $(`#dataRows tr[data-id="${paymentID}"]`);
            $row.removeClass("from-reservation");
            $row
              .find("td:nth-child(7)")
              .text("Fully Paid")
              .css("color", "green");
            $row.data("status", "Fully Paid");
            const badge = document.getElementById("reservation-count");
            if (badge) {
              let currentCount = parseInt(badge.textContent);
              currentCount--;
              if (currentCount > 0) {
                badge.textContent = currentCount;
              } else {
                badge.classList.remove("notification-unseen");
                badge.remove();
              }
            }
          });
        } else {
          Swal.fire("Error!", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to verify reservation: " + error, "error");
      },
    });
  });

  $(document).on("click", ".settle-row", function () {
    let paymentID = $(this).data("id");
    let guestName = $(this).data("guest");
    let totalAmount = parseFloat($(this).data("total"));
    let amountPay = parseFloat($(this).data("paid"));
    let status = $(this).data("status");

    $("#settlementPaymentID").val(paymentID);
    $("#settlementGuestName").val(guestName);
    $("#settlementTotalAmount").val(totalAmount.toFixed(2));
    $("#settlementAmountPay").val(amountPay.toFixed(2));
    $("#settlementStatus").val(status);

    updateSettlementUI(totalAmount, amountPay);
    $("#settlementModal").removeClass("hidden");
  });

  $("#settleFullPayment").click(function () {
    let totalAmount = parseFloat($("#settlementTotalAmount").val());
    $("#settlementAmountPay").val(totalAmount.toFixed(2));
    updateSettlementUI(totalAmount, totalAmount);
  });

  $("#settlementAmountPay").on("input", function () {
    let totalAmount = parseFloat($("#settlementTotalAmount").val());
    let amountPay = parseFloat($(this).val()) || 0;
    updateSettlementUI(totalAmount, amountPay);
  });

  $("#settlementForm").on("submit", function (e) {
    e.preventDefault();
    let paymentID = $("#settlementPaymentID").val();
    let totalAmount = parseFloat($("#settlementTotalAmount").val());
    let amountPay = parseFloat($("#settlementAmountPay").val());
    let paymentType = $("#settlementPaymentType").val();

    if (amountPay < totalAmount) {
      Swal.fire(
        "Error!",
        "Amount paid must equal or exceed the total amount to settle.",
        "error"
      );
      return;
    }

    let newStatus = "Settled";

    $.ajax({
      url: "./includes/UpdatePayment.php",
      type: "POST",
      data:
        $(this).serialize() +
        "&settle=1&status=" +
        encodeURIComponent(newStatus),
      success: function (response) {
        if (response.status === "success") {
          Swal.fire({
            title: "Success!",
            text: response.message,
            icon: "success",
          }).then(() => {
            $("#settlementModal").addClass("hidden");
            let $row = $(`#dataRows tr[data-id="${paymentID}"]`);
            $row.removeClass("settle-row").addClass("settled-row");
            $row.find("td:nth-child(7)").text(newStatus).css("color", "gray");
            $row.find("td:nth-child(6)").text(paymentType);
            $row
              .find("td:nth-child(4)")
              .text(
                "₱" + amountPay.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,")
              );
            $row.data("paid", amountPay);
            $row.data("method", paymentType);
            $row.data("status", newStatus);
            $row
              .find(".EditInfo")
              .addClass("opacity-50 cursor-not-allowed")
              .prop("disabled", true);
          });
        } else {
          Swal.fire("Error!", response.message, "error");
        }
      },
      error: function (xhr, status, error) {
        Swal.fire("Error!", "Failed to settle payment: " + error, "error");
      },
    });
  });

  function updateSettlementUI(totalAmount, amountPay) {
    const settleButton = $("#settleButton");
    const payFullButton = $("#settleFullPayment");
    const statusField = $("#settlementStatus");

    let status;
    if (amountPay < totalAmount) {
      status = "Downpayment";
    } else if (amountPay === totalAmount) {
      status = "Fully Paid";
    } else {
      status = "Overpaid";
    }
    statusField.val(status);

    if (amountPay < totalAmount) {
      settleButton
        .prop("disabled", true)
        .removeClass("hover:bg-orange-600")
        .addClass("opacity-50 cursor-not-allowed");
      payFullButton
        .prop("disabled", false)
        .removeClass("opacity-50 cursor-not-allowed")
        .addClass("hover:bg-yellow-600");
    } else {
      settleButton
        .prop("disabled", false)
        .removeClass("opacity-50 cursor-not-allowed")
        .addClass("hover:bg-orange-600");
      payFullButton
        .prop("disabled", true)
        .removeClass("hover:bg-yellow-600")
        .addClass("opacity-50 cursor-not-allowed");
    }
  }

  function calculateStayDuration(startDate, endDate) {
    let today = new Date();
    startDate = new Date(startDate);
    endDate = new Date(endDate);
    let days, text, color;

    if (today > endDate) {
      text = "Expired";
      color = "";
      days = -1;
    } else if (today < startDate) {
      let interval = startDate.getTime() - endDate.getTime();
      days = Math.ceil(Math.abs(interval) / (1000 * 60 * 60 * 24));
      text = days + " days";
      color = days >= 3 ? "#22c55e" : days === 2 ? "#ca8a04" : "#ef4444";
    } else {
      let interval = today.getTime() - endDate.getTime();
      days = Math.ceil(Math.abs(interval) / (1000 * 60 * 60 * 24));
      text = days + " days";
      color = days >= 3 ? "#22c55e" : days === 2 ? "#ca8a04" : "#ef4444";
    }

    return { text: text, color: color, days: days };
  }
});
