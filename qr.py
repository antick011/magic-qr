import qrcode
import os

# Function to generate QR code
def generate_qr(customer_id):
    feedback_form_url = f"http://localhost/magicqr/customer/feedback_form.php?customer_id=6"   
    # {customer_id}

    # Create QR code object
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr.add_data(feedback_form_url)
    qr.make(fit=True)

    # Create an image from the QR Code
    img = qr.make_image(fill='black', back_color='white')

    # Save the image with a unique name based on customer ID
    output_dir = "generated_qr_codes/"
    os.makedirs(output_dir, exist_ok=True)
    img.save(f"{output_dir}feedback_qr_6.png")

# Example: Generate QR code for customer ID 3
generate_qr(3)
