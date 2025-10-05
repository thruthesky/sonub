/**
 * 테스트 사용자를 생성하는 스크립트
 * https://firebase.google.com/docs/auth/admin/manage-users#create_a_user
 */

import { getAuth } from "firebase-admin/auth";

// Firebase Admin SDK 초기화
import { initializeApp } from "firebase-admin/app";
initializeApp();

const users = [
  { email: "apple@test.com", number: "+11234567890" },
  { email: "banana@test.com", number: "+11234567891" },
  { email: "cherry@test.com", number: "+11234567892" },
  { email: "durian@test.com", number: "+11234567893" },
  { email: "elderberry@test.com", number: "+11234567894" },
  { email: "fig@test.com", number: "+11234567895" },
  { email: "grape@test.com", number: "+11234567896" },
  { email: "honeydew@test.com", number: "+11234567897" },
  { email: "jackfruit@test.com", number: "+11234567898" },
  { email: "kiwi@test.com", number: "+11234567899" },
  { email: "lemon@test.com", number: "+11234567900" },
  { email: "mango@test.com", number: "+11234567901" },
];

async function runCode() {
  for (const u of users) {
    try {
      const userRecord = await getAuth().getUserByEmail(u.email);
      await getAuth().deleteUser(userRecord.uid);
    } catch (error) {
      if (error.code !== "auth/user-not-found") {
        console.log("Error deleting user:", error);
      }
    }
    // Create a new user with the same email and phone number

    getAuth()
      .createUser({
        uid: u.email.split("@")[0],
        email: u.email,
        emailVerified: false,
        phoneNumber: u.number,
        password: "12345a,*",
        displayName:
          u.email.split("@")[0].charAt(0).toUpperCase() +
          u.email.split("@")[0].slice(1),
        photoURL: "https://picsum.photos/200/300",
        disabled: false,
      })
      .then((userRecord) => {
        // See the UserRecord reference doc for the contents of userRecord.
        console.log("Successfully created new user:", userRecord.uid);
      })
      .catch((error) => {
        console.log("Error creating new user:", error);
      });
  }
}

runCode();
