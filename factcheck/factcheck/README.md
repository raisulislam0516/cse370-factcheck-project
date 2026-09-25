# Fact Check Project — Setup Guide

## যা লাগবে
- **XAMPP** ইনস্টল করুন: https://www.apachefriends.org (এতে PHP + MySQL + phpMyAdmin সব একসাথে থাকে)

## ধাপে ধাপে চালানোর নিয়ম

1. XAMPP ইনস্টল করে **Apache** আর **MySQL** দুটোই Start করুন (XAMPP Control Panel থেকে)

2. এই পুরো `factcheck` ফোল্ডারটা কপি করে XAMPP-এর `htdocs` ফোল্ডারে রাখুন:
   - Windows: `C:\xampp\htdocs\factcheck`
   - Mac: `/Applications/XAMPP/htdocs/factcheck`

3. ব্রাউজারে যান: `http://localhost/phpmyadmin`
   - উপরে "Import" ট্যাবে ক্লিক করুন
   - `database.sql` ফাইলটা বেছে নিয়ে "Go" চাপুন — এতে database ও সব table তৈরি হয়ে যাবে

4. ব্রাউজারে যান: `http://localhost/factcheck`
   - Signup করে একটা অ্যাকাউন্ট বানান
   - Login করে Claim submit করে দেখুন

## নতুন যোগ হয়েছে (Login/Register একই কার্ডে, Tab-style)
- 🎨 Login পেজ এখন সম্পূর্ণ redesign করা হয়েছে — উপরে গোল লোগো আইকন, নিচে **Sign In / Register** দুটো ট্যাব একই কার্ডে (আলাদা পেজে যাওয়া লাগে না, ক্লিক করলেই ফর্ম বদলে যায়)
- `signup.php` এখন শুধু `login.php`-তে redirect করে (পুরনো link ভাঙবে না)
- Login/Register সব logic এখন `login.php`-এই আছে


- এখন **Claim জমা দেওয়ার ফর্মেই** (আগে শুধু বিবরণ লেখা যেত) সরাসরি একটা **screenshot/ছবি (ঐচ্ছিক)** সংযুক্ত করা যায় — আলাদা করে পরে Details পেজে গিয়ে Media যোগ করার দরকার নেই
- Claim submit করলে, screenshot দেওয়া থাকলে সেটা automatically MEDIA টেবিলে সেই claim-এর সাথে link হয়ে যায়
- Submit করার পর সরাসরি সেই claim-এর Details পেজে নিয়ে যাওয়া হয়, যেখানে screenshot-সহ সবকিছু দেখা যাবে


- 🎨 নতুন font (Poppins + Hind Siliguri), hero banner, hover animation সহ পুরো সাইট আরও আকর্ষণীয় করা হয়েছে
- Signup-এ এখন শুধু **User** ও **Fact Checker** — role বেছে নেওয়া যায় (Admin অপশন সরিয়ে দেওয়া হয়েছে; Admin এখন শুধু phpMyAdmin দিয়ে manually সেট করতে হবে, যা বাস্তব অ্যাপেও normal practice)
- 🔍 **"সব Claim ঘুরে দেখুন" ফিচার** — Dashboard-এ prominent hero banner থেকে সরাসরি সব Claim card আকারে browse করা যায়, Claim জমা দেওয়া ছাড়াই। কোনো user নিজে claim submit না করেও অন্যদের সব claim দেখতে পারবেন
- Claim list এখন table-এর বদলে সুন্দর **card-grid feed**, প্রতিটা card-এ category, status, submitter, date, আর Details বাটন
- Status অনুযায়ী filter chip (সবগুলো / Pending / Verified / Rejected / Flagged) যোগ করা হয়েছে


- 🎨 রং আরও **soft/হালকা** করা হয়েছে (আগের গাঢ় নেভি এখন হালকা নীল-ধূসর)
- Sidebar-এ এখন **প্রতিটা entity-র নিজস্ব পেজ** আছে — ক্লিক করলেই সেই entity-র সব data এক জায়গায় দেখা যাবে:
  - 📋 Claim, 📄 Evidence, 🖼️ Media, 🔖 Source, 💬 Comment, ✅ Fact-Check
  - (নতুন Evidence/Media যোগ করতে হলে এখনো কোনো নির্দিষ্ট claim-এর Details পেজে যেতে হবে, কারণ এগুলো একটা claim-এর সাথে যুক্ত)
- 🚩 **Flag সিস্টেম (Admin only)** — Admin এখন যেকোনো Claim-কে "Flagged / Checked" মার্ক করতে পারবে — এটা Fact-Check verdict থেকে আলাদা, শুধু বোঝাতে যে admin claim-টা review করেছে। Sidebar-এ **"Flagged Claims"** লিংক দিয়ে সব flagged claim একসাথে দেখা যাবে (শুধু admin-দের জন্য দেখা যাবে)

### ⚠️ Database migration লাগবে (নতুন `flagged` কলাম)
এই zip-এ **`migration_add_flagged.sql`** নামে একটা আলাদা ফাইল আছে (আগের `migration_add_reactions.sql` থেকে আলাদা, যাতে "column already exists" error না আসে)।

- phpMyAdmin → `factcheck_db` → **SQL** ট্যাব → `migration_add_flagged.sql` এর কনটেন্ট paste করে **Go** চাপুন

(যদি আগে কখনো `reactions` কলাম যোগ না করে থাকেন, তাহলে `migration_add_reactions.sql`-ও একইভাবে রান করুন)

- 📌 **Sidebar navigation** — এখন প্রতিটা পেজের বাম পাশে একটা সাইডবার আছে (Dashboard, সব Claim, নতুন Claim, Sources, আর fact_checker/admin হলে "Pending Verdict" লিংক)
- 📊 **Dashboard পেজ** (`dashboard.php`) — Login করার পর এখানেই প্রথমে আসবেন। এখানে দেখা যাবে: মোট Claim, Pending/Verified/Rejected এর সংখ্যা, আপনার নিজের জমা দেওয়া Claim সংখ্যা, দ্রুত অপশন (Quick Links), এবং সাম্প্রতিক ৫টা Claim
- 😀 **Emoji Reaction** — প্রতিটা Comment-এর নিচে এখন 👍 ❤️ 😂 😮 😢ইমোজি বাটন আছে, ক্লিক করলে reaction count বেড়ে যাবে

### ⚠️ যদি আগে থেকে database.sql import করা থাকে
Comment-এ emoji reaction সেভ করার জন্য একটা নতুন কলাম লাগবে (`reactions`)। এই zip-এ **`migration_add_reactions.sql`** নামে একটা আলাদা ফাইল আছে —
1. phpMyAdmin-এ গিয়ে `factcheck_db` সিলেক্ট করুন
2. উপরে **SQL** ট্যাবে ক্লিক করুন
3. `migration_add_reactions.sql` এর ভেতরের লাইনটা paste করে Go চাপুন

(নতুন করে পুরো database.sql আবার import করলেও চলবে, কিন্তু তাহলে আগের সব data মুছে নতুন খালি টেবিল তৈরি হবে — তাই migration ফাইলটাই ব্যবহার করা ভালো)

- 🔍 ম্যাগনিফাইং গ্লাস লোগো সহ নতুন "TruthCheck" ব্র্যান্ডিং, নেভি + টিল কালার থিম
- Signup পেজে এখন **role বেছে নেওয়ার option** আছে (User / Fact Checker / Admin) — টেস্ট করার সুবিধার জন্য, প্রোডাকশন অ্যাপে এটা সাধারণত admin নিজে সেট করে
- **Screenshot/Media সরাসরি আপলোড** করা যায় এখন — আগে শুধু URL দেওয়া যেত, এখন claim-এর Details পেজ থেকে সরাসরি কম্পিউটার থেকে ছবি/ভিডিও (jpg, png, gif, webp, mp4) বেছে নিয়ে আপলোড করা যাবে। আপলোড করা ছবি সরাসরি thumbnail আকারে দেখা যাবে।
- আপলোড হওয়া ফাইলগুলো সেভ হয় `uploads/` ফোল্ডারে — এই ফোল্ডারে write permission থাকা দরকার (XAMPP-এ ডিফল্টভাবে থাকে)

## এখন পর্যন্ত যা কাজ করছে (সবগুলো ফিচার — Full CRUD)

1. **Authentication** — Signup / Login / Logout
2. **Claim Management** (`claims/`) — Create, Read (list), Update, Delete
3. **Source Management** (`source/`) — Create, Read, Update, Delete (independent module)
4. **Evidence Management** — একটা claim-এর "Details" পেজ থেকে (Create, Read, Delete) — Source-এর সাথে optional link করা যায়
5. **Media Management** — claim-এর Details পেজ থেকে (Create, Read, Delete)
6. **Comment** — claim-এর Details পেজ থেকে (Create, Read, Delete — নিজের comment বা admin)
7. **Fact-Check Verdict** — claim-এর Details পেজ থেকে (শুধু `fact_checker` বা `admin` role করতে পারবে) — verdict দিলে claim-এর status automatically আপডেট হয়ে যায়

সব claim-এর তালিকায় **"Details"** লিংকে ক্লিক করলে সেই claim-এর Evidence, Media, Comment, Fact-check — সবকিছু এক জায়গায় দেখা ও manage করা যাবে (`claims/view.php`)।

## Role নিয়ে
- নতুন signup করা সবাই ডিফল্টভাবে `user` role পায়
- কাউকে `admin` বা `fact_checker` বানাতে চাইলে phpMyAdmin-এ গিয়ে USER টেবিলে সেই ইউজারের `role` কলাম ম্যানুয়ালি বদলে `admin` বা `fact_checker` লিখে দিন
- Fact-check verdict দেওয়ার ফিচারটা টেস্ট করতে হলে অন্তত একটা account-কে `fact_checker` বানিয়ে নিন

## টেস্ট করার সহজ উপায়
1. দুটো account বানান — একটা normal `user`, একটাকে phpMyAdmin দিয়ে `fact_checker` বানিয়ে দিন
2. Normal user দিয়ে Login করে একটা Claim submit করুন, তারপর সেই Claim-এর Details-এ গিয়ে Evidence/Media/Comment যোগ করুন
3. fact_checker account দিয়ে Login করে সেই একই Claim-এ গিয়ে Verdict দিন — দেখবেন claim-এর status বদলে যাচ্ছে
