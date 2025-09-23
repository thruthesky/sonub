/**
 * Phone number validation for login
 *
 * @logic
 * - Removes special characters from the input phone number and generates country dial codes for Korea (+82) or Philippines (+63).
 * - If the input phone number starts with 010, this value is changed to +8210.
 * - If the input phone number starts with 09, this value is changed to +639.
 * - If the input phone number starts with +, the international phone number is used as is.
 * - For both Korean and Philippine phone numbers, the total length of the phone number must be 13 digits.
 *   Examples: '+821012345678' or '+639171234567'
 *
 * @param {string} phone_number - The phone number entered by the user
 *
 * @return {string} - Returns a valid phone number or throws an error code.
 * - If there is an error, throws an error like throw new Error('xxx').
 *
 * @example
 * ```javascript
<script>
    $(() => {
        console.log("DOM ready");
        alert(check_login_phone_number('+8210123456-78'));
    });
</script>
 * ```
 */

function check_login_phone_number(phone_number) {
  phone_number = phone_number.trim();

  if (!phone_number) {
    throw new Error("empty-phone-number");
  }

  // Remove special characters
  phone_number = phone_number.replace(/[^0-9+]/g, "");

  // Phone number validation
  if (phone_number.startsWith("010")) {
    phone_number = phone_number.replace("010", "+8210");
    // Check phone number length
    if (phone_number.length < 13) {
      throw new Error("too-short-phone-number");
    } else if (phone_number.length > 13) {
      throw new Error("too-long-phone-number");
    }
  } else if (phone_number.startsWith("09")) {
    phone_number = phone_number.replace("09", "+639");
  } else if (phone_number.startsWith("+")) {
    // Use international phone number as is
  } else {
    throw new Error("invalid-phone-number");
  }

  return phone_number; // Return valid phone number
}
