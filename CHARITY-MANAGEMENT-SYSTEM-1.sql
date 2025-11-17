create database CHARITY_MANAGEMENT_SYSTEM
USE CHARITY_MANAGEMENT_SYSTEM
--main table
use CHARITY_MANAGEMENT_SYSTEM

create table CHARITY(
CHARITY_ID int primary key ,
CHARITY_Name varchar(50) not null,
CHARITY_Address varchar(50) not null,
CHARITY_Dateofestablish date,
CHARITY_Information text,
EVENTS_ID int,
BranchID int);

--donor table 
create table DONOR(
DONOR_ID int primary key,
DONOR_Name nvarchar(50)not null,
DONOR_information text);
--donation table 
create table DONATION(
DONATION_ID int primary key,
DONATION_Name nvarchar(50) not null,
DONATION_Type nvarchar(50) not null,
DONATION_Amount int not null,
DONOR_ID int,
STAFF_ID int,
foreign key(DONOR_ID) references Donor(DONOR_ID),
foreign key(STAFF_ID) references Staff(STAFF_ID));

create table EVENTS_T(
EVENTS_ID int primary key, 
EVENTS_Name nvarchar(50) not null,
EVENTS_Type nvarchar(50) not null,
EVENTS_Location nvarchar(50) not null,
EVENTS_Description text,
BRANCH_ID int,
CHARITY_ID int,
foreign key(BRANCH_ID) references Branch(BRANCH_ID),
foreign key (CHARITY_ID) references Charity(Charity_ID));
--branch table
create table BRANCH(
BRANCH_ID int primary key,
BRANCH_Name varchar(50) not null,
BRANCH_Location varchar(50) not null,
BRANCH_Phone_Num int not null,
CHARITY_ID int,
foreign key (CHARITY_ID) references CHARITY(CHARITY_ID));
-- project table
create table PROJECT(
PROJECT_ID int primary key,
PROJECT_Name nvarchar(50) not null,
PROJECT_Description text not null,
PROJECT_Location nvarchar(50) not null,
PROJECT_Date date not null,
PROJECT_Requried_budged int  not null, 
BRANCH_ID int,
foreign key ( Branch_ID)references Branch(Branch_ID));
--reports table
create table REPORTS(
REPORT_ID int primary key, 
REPORT_Title text not null,
REPORT_Date date not null,
REPORT_Type nvarchar(50) not null,
PROJECT_ID int,
EVENTS_ID int,
foreign key (PROJECT_ID) references Project(PROJECT_ID),
foreign key(EVENTS_ID) references EventS_T(EVENTS_ID));
-- volunteer table
create table VOLUNTEER(
VOLUNTEER_ID int primary key,
VOLUNTEER_Name nvarchar(50) not null,
VOLUNTEER_phone int not null,
BRANCH_ID int,
PROJECT_ID int,
foreign key (BRANCH_ID) references Branch(BRANCH_ID),
foreign key(PROJECT_ID) references Project(PROJECT_ID));
--status table
create table STATUS_T(
STATUS_T int primary key,
STATUS_Description text,
STATUS_lastUpdate date,
EVENTS_ID int,
PROJECT_ID int,
foreign key(EVENTS_ID)references EVENTS_T(EVENTS_ID),
foreign key(PROJECT_ID) references Project(PROJECT_ID));
--balance table
create table BALANCE(
BALANCE_ID int primary key,
BALANCE_Project nvarchar(50) not null,
BALANCE_Event nvarchar(50) not null,
BALANCE_Amount int  not null,
BALANCE_TransictionDate date not null,
BALANCE_Description text,
STAFF_ID int,
BRANCH_ID int,
foreign key(STAFF_ID)references STAFF (STAFF_ID),
foreign key (BRANCH_ID) references Branch(Branch_ID));
--staff table
create table STAFF(
STAFF_ID int primary key,
STAFF_Name nvarchar(50) not null,
STAFF_PhoneNum int not null,
STAFF_Email varchar(50),
STAFF_Bio text,
STAFF_Salary float,
BRANCH_ID int,
foreign key (BRANCH_ID)references Branch(BRANCH_ID));
--beneficiary table
create table BENEFICIARY(
BENEFICIARY_ID int primary key,
BENEFICIARY_Name nvarchar(50) not null,
BENEFICIARY_PhoneNum int,
BENEFICIARY_Address nvarchar(50) not null,
BENEFICIARY_TypeOfDonation text not null);
--Associative tables
create table CHARITY_DONOR(
CHARITY_ID int,
DONOR_ID int,
foreign key( CHARITY_ID) references Charity(CHARITY_ID),
foreign key(DONOR_ID) references Donor(DONOR_ID));

create table BENEFICIARY_DONATION(
BENEFICIARY_iD INT,
DONATION_ID INT,
foreign key (BENEFICIARY_iD )references BENEFICIARY(BENEFICIARY_iD ),
foreign key (DONATION_ID) references DONATION( DONATION_ID));

create table BENEFICIARY_EVENTS(
BENEFICIARY_iD INT,
EVENTS_ID INT,
foreign key (BENEFICIARY_iD )references BENEFICIARY(BENEFICIARY_iD ),
foreign key (EVENTS_ID) references EVENTS_T(EVENTS_ID));
create table BALANCE_EVENT(
BALANCE_ID INT,
EVENTS_ID INT,
foreign key (BALANCE_ID)references Balance(BALANCE_ID),
foreign key (EVENTS_ID) references EVENTS_T(EVENTS_ID));
 create table BALACE_PROJECT(
 BALANCE_ID INT,
 PROJECT_ID INT,
 foreign key (BALANCE_ID) references Balance(BALANCE_ID),
 foreign key(PROJECT_ID) references PROJECT(PROJECT_iD));

 create table STAFF_PROJECT(
 STAFF_ID INT,
 PROJECT_ID INT,
 foreign key(STAFF_ID)REFERENCES STAFF(STAFF_ID),
 FOREIGN KEY (PROJECT_ID) REFERENCES PROJECT(PROJECT_ID));
 CREATE TABLE DONATION_BALANCE(
 DONATION_ID INT,
 BALANCE_ID INT,
 FOREIGN KEY(DONATION_ID) REFERENCES DONATION(DONATION_ID),
 FOREIGN KEY ( BALANCE_ID)REFERENCES BALANCE(BALANCE_ID));
 CREATE TABLE DONATION_BALANCE(
 DONATION_ID INT,
 BALANCE_ID INT,
 FOREIGN KEY  (DONATION_ID) REFERENCES DONATION(DONATION_ID),
 FOREIGN KEY(BALANCE_ID) REFERENCES BALANCE(BALANCE_ID));
 ----------------
-- insert data
select*from charity
insert into CHARITY( CHARITY_ID,Charity_Name,Chartiy_Address,Chartiy_Dateofestablish,Chartiy_Information)
values( 1,'MBQ','3rd Road Darlaman Kabul Afghanistan','2022-6-8','Support Needy Childreen'),
(2, 'Helping Hands', 'Kabul City Center', '2005-03-15', 'Provides food and clothing to underprivileged families.'),
(3, 'Afghan Aid', 'Herat Main Market', '2008-07-22', 'Supports education and health programs across Afghanistan.'),
(4, 'Relief for All', 'Mazar-i-Sharif, Balkh', '2010-01-10', 'Disaster relief and emergency support organization.'),
(5, 'Hope Foundation', 'Kandahar District 1', '2007-05-05', 'Focuses on shelter and housing projects for displaced families.'),
(6, 'Care and Support', 'Jalalabad, Nangarhar', '2012-09-12', 'Provides school supplies and education support.'),
(7, 'Life Aid', 'Fayzabad, Badakhshan', '2009-11-20', 'Water and sanitation projects for rural communities.'),
(8, 'Health First', 'Charikar, Parwan', '2011-02-18', 'Health awareness campaigns and medical aid.'),
(9, 'Future Builders', 'Kunduz City Center', '2013-06-25', 'Vocational training and youth empowerment programs.'),
(10, 'Green Earth', 'Taloqan, Takhar', '2006-08-30', 'Community gardening and environmental projects.'),
(11, 'Safe Haven', 'Pul-e-Alam, Logar', '2004-12-05', 'Winter relief programs for vulnerable families.'),
(12, 'Bright Future', 'Maidan Shahr, Wardak', '2015-04-10', 'Support programs for orphans and children in need.'),
(13, 'Community Aid', 'Mehtarlam, Laghman', '2003-03-15', 'Disaster response and emergency assistance.'),
(14, 'Youth Empowerment', 'Gardez, Paktia', '2016-07-20', 'Microfinance and small loans for youth and women.'),
(15, 'Education for All', 'Sharana, Paktika', '2008-09-08', 'Education outreach and school support programs.'),
(16, 'Energy for Life', 'Lashkargah, Helmand', '2010-11-11', 'Clean energy initiatives and awareness campaigns.'),
(17, 'Refugee Support', 'Ghazni City', '2014-01-25', 'Support for refugees including food, shelter, and health aid.'),
(18, 'Mind Care', 'Aybak, Samangan', '2012-05-14', 'Mental health support and counseling programs.'),
(19, 'Livelihood Aid', 'Pul-e-Khumri, Baghlan', '2009-08-19', 'Livelihood development and vocational support.'),
(20, 'Vaccinate Kids', 'Mahmud Raqi, Kapisa', '2011-12-01', 'Mass vaccination campaigns for children.'),
(21, 'Build Together', 'Farah City', '2007-06-30', 'Community infrastructure repair and construction projects.');
select*from DONOR
insert into DONOR(DONOR_ID,DONOR_Name,DONOR_information)
values( 1,'Ahmad Bilal', 'Head of Omari Company'),
(2,'Basit','1 TV  Manager'),
(3, 'Sarwer','Chief Operating Officer'),
(4,'Reza','General Manager'),
(5,'Akram',' Vice Presedint'),
(6,'Arshad ahmad','Data Analyst'),
(7,'Miwand','CRM Manager'),
(8,'Mustafa','IT Manager'),
(9,'Shafeh ulllah','System Administrator'),
(10,'Roshan','Network Engineer'),
(11,'Qurban','Database Admanistrator'),
(12,'Musawer','Softwere Devolaper'),
(13, 'Murtaza','Web Devolaper'),
(14,'Jawed','Mobile App Devolaper'),
(15,'Mohammad ullah','Project Manager'),
(16,'Alisha','Cyber Security'),
(17,'Ali Sajad','Cloud Administrator'),
(18,'Said Zaki','Doctor'),
(19, 'Khalil','Director of Marketing'),
(20,'Rahim','Program Manager'),
(21,'Alisena','Opreation Manager');
delete from  DONOR where  donor_id=120
select*from BENEFICIARY
 insert into BENEFICIARY(BENEFICIARY_ID,BENEFICIARY_Name,BENEFICIARY_PhoneNum,BENEFICIARY_Address,BENEFICIARY_TypeOfDonation)
values(1,'Ahmad Khan', '0700123001', 'Kabul', 'Food'),
(2, 'Mohammad Ali', '0700123002', 'Badghis', 'Clothing'),
(3, 'Rahim Gul', '0700123003', 'Ghazni', 'School supplies'),
(4, 'Hamidullah', '0700123004', 'Kunar', 'Food'),
(5, 'Farid Akbari', '0700123005', 'Frah', 'Shelter'),
(6, 'Saifullah', '0700123006', 'Paktika', 'Clothing'),
(7, 'Bilal Hussain', '0700123007', 'Paktya', 'Medical supplies'),
(8, 'Karim Zaman', '0700123008', 'Helmand', 'Food'),
(9, 'Nasir Ahmad', '0700123009', 'Faryab', 'Water & Hygiene'),
(10, 'Wali Khan', '0700123010', 'Balkh', 'Shelter'),
(11, 'Habibullah', '0700123011', 'Nemroz', 'School supplies'),
(12, 'Javed Safi', '0700123012', 'Ghor', 'Food'),
(13, 'Asadullah', '0700123013', 'Nangarhar', 'Clothing'),
(14, 'Ziauddin', '0700123014', 'Kunar', 'Medical supplies'),
(15, 'Rafiq Khan', '0700123015', 'Laghman', 'Food'),
(16, 'Hedayatullah', '0700123016', 'Herat', 'Shelter'),
(17, 'Shafiq Ahmad', '0700123017', 'Balkh', 'Water & Hygiene'),
(18, 'Naeemullah', '0700123018', 'Kabul', 'School supplies'),
(19, 'Imran Safi', '0700123019', 'Logar', 'Clothing'),
(20, 'Gul Mohammad', '0700123020', 'Kandahar', 'Food');

select*from BRANCH
INSERT INTO branch (branch_id, branch_name, BRANCH_Location, BRANCH_Phone_Num,CHARITY_ID)
VALUES
(1, 'Kabul', 'Karte 4, Kabul City', '0202101001'),
(2, 'Herat', 'District 3, Herat City', '0402201002'),
(3, 'Balkh', 'Charbolak, Mazar-i-Sharif', '0502301003'),
(4, 'Kandahar', 'District 1, Kandahar City', '0302401004'),
(5, 'Nangarhar', 'Jalalabad Main Road', '0602501005'),
(6, 'Badakhshan', 'Fayzabad Central Market', '0702601006'),
(7, 'Parwan', 'Charikar City Center', '0802701007'),
(8, 'Kunduz', 'District 2, Kunduz City', '0902801008'),
(9, 'Takhar', 'Taloqan Main Bazaar', '0912901009'),
(10, 'Logar', 'Pul-e-Alam District 1', '0922101010'),
(11, 'Wardak', 'Maidan Shahr Center', '0932201011'),
(12, 'Laghman', 'Mehtarlam Bazaar', '0942301012'),
(13, 'Paktia', 'Gardez City Center', '0952401013'),
(14, 'Paktika', 'Sharana District 1', '0962501014'),
(15, 'Helmand', 'Lashkargah Main Market', '0972601015'),
(16, 'Ghazni', 'Ghazni City, District 2', '0982701016'),
(17, 'Samangan', 'Aybak Bazaar', '0992801017'),
(18, 'Baghlan', 'Pul-e-Khumri Center', '0702901018'),
(19, 'Kapisa', 'Mahmud Raqi Bazaar', '0722101019'),
(20, 'Farah', 'Farah City, District 1', '0732201020');
select* from STAFF
INSERT INTO staff (staff_id, STAFF_Name, STAFF_PhoneNum, STAFF_Email, STAFF_Bio, STAFF_Salary, BRANCH_ID) VALUES
(1, 'Ahmad Jan', '0700123101', 'ahmad.jan@gmail.com', 'Experienced logistics officer.', 25000, 1),
(2, 'Mohammad Yousuf', '0700123102', 'm.yousuf@gmail.com', 'Field coordinator with 5 years experience.', 28000, 2),
(3, 'Rahimullah', '0700123103', 'rahimullah@gmail.com', 'Finance assistant in humanitarian projects.', 30000, 3),
(4, 'Hamidullah', '0700123104', 'hamidullah@gmail.com', 'Project officer for rural development.', 27000, 4),
(5, 'Farid Ahmad', '0700123105', 'farid.ahmad@gmail.com', 'Community mobilizer with strong outreach skills.', 26000, 5),
(6, 'Saifullah', '0700123106', 'saifullah@gmail.com', 'IT support and database assistant.', 32000, 6),
(7, 'ALI', '0700123107', 'bilal.ahmad@gmail.com', 'Monitoring and evaluation field officer.', 29000, 7),
(8, 'Karimullah', '0700123108', 'karimullah@gmail.com', 'Health program assistant with NGO experience.', 31000, 8),
(9, 'Nasirullah', '0700123109', 'nasirullah@gmail.com', 'Senior field supervisor in education sector.', 35000, 9),
(10, 'Wali Mohammad', '0700123110', 'wali.mohammad@gmail.com', 'Driver and logistics support.', 22000, 10),
(11, 'Habib Rahman', '0700123111', 'habib.rahman@gmail.com', 'Procurement officer with 4 years experience.', 33000, 11),
(12, 'Javed Khan', '0700123112', 'javed.khan@gmail.com', 'HR assistant managing staff files.', 28000, 12),
(13, 'Asadullah', '0700123113', 'asadullah@gmail.com', 'Security focal point with field knowledge.', 27000, 13),
(14, 'Zia Rahman', '0700123114', 'zia.rahman@gmail.com', 'Driver and field facilitator.', 21000, 14),
(15, 'Rafiullah', '0700123115', 'rafiullah@gmail.com', 'Admin and finance support.', 30000, 15),
(16, 'Hedayatullah', '0700123116', 'hedayatullah@gmail.com', 'Education program assistant.', 29000, 16),
(17, 'Shafiullah', '0700123117', 'shafiullah@gmail.com', 'Health outreach coordinator.', 34000, 17),
(18, 'Naeem Rahman', '0700123118', 'naeem.rahman@gmail.com', 'Field logistician with training background.', 31000, 18),
(19, 'Imranullah', '0700123119', 'imranullah@gmail.com', 'Monitoring assistant for projects.', 28000, 19),
(20, 'Gul Rahman', '0700123120', 'gul.rahman@gmail.com', 'Office support and document handler.', 23000, 20);
 select* from DONATION
 insert into DONATION( DONATION_ID,DONATION_Type,DONATION_Amount,DONOR_ID,STAFF_ID)
 VALUES(1, 'Food', 5000000, 1, 1),
(2, 'Clothing', 100000, 2, 2),
(3, 'Medical Supplies', 8000000, 3, 3),
(4, 'Shelter Materials', 120000000, 4, 4),
(5, 'School Supplies', 250000, 5, 5),
(6, 'Food', 8000000, 6, 6),
(7, 'Clothing', 350000, 7, 7),
(8, 'Medical Supplies', 90000, 8, 8),
(9, 'Water & Hygiene', 60000, 9, 9),
(10, 'Food', 400000, 10, 10),
(11, 'Shelter Materials', 11000, 11, 11),
(12, 'Clothing', 28000, 12, 12),
(13, 'Medical Supplies', 19500, 13, 13),
(14, 'School Supplies', 32500, 14, 14),
(15, 'Food', 120000, 15, 15),
(16, 'Clothing', 102700, 16, 16),
(17, 'Water & Hygiene', 555500, 17, 17),
(18, 'Medical Supplies', 70200, 18, 18),
(19, 'Shelter Materials', 103000, 19, 19),
(20, 'Food', 60000, 20, 20);
select*from DONOR
select* from DONOR where DONOR_ID=2
alter table donor add DONOR_PhoneNum int;
alter  table donor add DONOR_Email Nvarchar(50);
alter table donor add DONOR_LastName nvarchar(50);

select*from PROJECT
insert into PROJECT( PROJECT_ID,PROJECT_Name,PROJECT_Description,PROJECT_Location,PROJECT_Date,PROJECT_Requried_budged,BRANCH_ID)
values(1,1, 'Food Distribution', 'Kabul City Center', '2025-01-05', 50000, 1),
(2,2, 'Education Outreach', 'Herat Market', '2025-01-06', 30000, 2),
(3,3, 'Medical Aid', 'Balkh Clinic', '2025-01-07', 70000, 3),
(4,4,'Shelter Setup', 'Kandahar District 1', '2025-01-08', 120000, 4),
(5,5,'School Supplies', 'Nangarhar Town', '2025-01-09', 25000, 5),
(6,6, 'Water', 'Badakhshan Village', '2025-01-10', 60000, 6),
(7, 7,'Health Awareness Campaign', 'Parwan City', '2025-01-11', 40000, 7),
(8,8, 'Vocational Training', 'Kunduz Center', '2025-01-12', 35000, 8),
(9, 9,'Community Gardening', 'Takhar District', '2025-01-13', 20000, 9),
(10, 10,'Winter Relief Program', 'Logar Town', '2025-01-14', 80000, 10),
(11,11, 'Orphan Support', 'Wardak City', '2025-01-15', 30000, 11),
(12, 12,'Disaster Response', 'Laghman District', '2025-01-16', 150000, 12),
(13, 13,'Microfinance Support', 'Paktia Town', '2025-01-17', 50000, 13),
(14, 14,'Education Outreach', 'Paktika Village', '2025-01-18', 40000, 14),
(15, 15,'Clean Energy Initiative', 'Helmand Town', '2025-01-19', 90000, 15),
(16,16, 'Refugee Assistance', 'Ghazni Camp', '2025-01-20', 100000, 16),
(17,17, 'Mental Health Support', 'Samangan Center', '2025-01-21', 35000, 17),
(18, 18,'Livelihood Development', 'Baghlan Town', '2025-01-22', 45000, 18),
(19, 19,'Vaccination Program', 'Kapisa District', '2025-01-23', 70000, 19),
(20, 20,'Infrastructure Repair', 'Farah City', '2025-01-24', 120000, 20);
select*from EVENTS_T
INSERT INTO EVENTS_T (EVENTS_ID, EVENTS_Name, EVENTS_Type, EVENTS_Location, EVENTS_Description, BRANCH_ID, CHARITY_ID)
values(1, 'Food for Families', 'Food Drive', 'Kabul City Center', 'Distribution of food packages to local families', 1, 1),
(2, 'Winter Clothes Aid', 'Clothing Drive', 'Herat Main Market', 'Winter clothing distribution for children', 2, 2),
(3, 'Rural Health Camp', 'Medical Aid', 'Balkh District Clinic', 'Free medical camp for rural areas', 3, 3),
(4, 'Emergency Shelter Setup', 'Shelter Project', 'Kandahar District 1', 'Temporary shelters for displaced families', 4, 4),
(5, 'Back to School', 'Education Support', 'Nangarhar Town', 'Providing school kits for students', 5, 5),
(6, 'Water & Sanitation', 'Health & Hygiene', 'Badakhshan Village', 'Clean water installation and hygiene promotion', 6, 6),
(7, 'Community Health Awareness', 'Health Campaign', 'Parwan City', 'Health awareness sessions in local communities', 7, 7),
(8, 'Skill Development Program', 'Training', 'Kunduz Center', 'Vocational training for youth and women', 8, 8),
(9, 'Green City Gardens', 'Environmental Project', 'Takhar District', 'Urban community gardening project', 9, 9),
(10, 'Winter Relief Aid', 'Relief Program', 'Logar Town', 'Winter relief program for vulnerable families', 10, 10),
(11, 'Orphan Support Initiative', 'Social Support', 'Wardak City', 'Support program for orphans', 11, 11),
(12, 'Disaster Relief Operation', 'Emergency Aid', 'Laghman District', 'Disaster response and emergency assistance', 12, 12),
(13, 'Women Microfinance Program', 'Financial Aid', 'Paktia Town', 'Microfinance and small loans for women', 13, 13),
(14, 'Rural Education Outreach', 'Education Support', 'Paktika Village', 'Education outreach in rural areas', 14, 14),
(15, 'Solar Energy Initiative', 'Environmental Project', 'Helmand Town', 'Clean energy awareness and installation', 15, 15),
(16, 'Refugee Aid Program', 'Relief Program', 'Ghazni Camp', 'Refugee assistance program including food & shelter', 16, 16),
(17, 'Mental Health Awareness', 'Health Campaign', 'Samangan Center', 'Mental health counseling sessions', 17, 17),
(18, 'Livelihood Development Project', 'Training', 'Baghlan Town', 'Livelihood support and vocational training', 18, 18),
(19, 'Child Vaccination Program', 'Medical Aid', 'Kapisa District', 'Mass vaccination drive for children', 19, 19),
(20, 'Community Infrastructure Repair', 'Construction', 'Farah City', 'Repair of community infrastructure', 20, 20);
select*from EVENTS_T

select * from branch 
update BRANCH set CHARITY_ID=3 where BRANCH_ID=4
select*from BALANCE
insert into BALANCE( BALANCE_ID,BALANCE_Project,BALANCE_Event,BALANCE_Amount,BALANCE_TransictionDate,BALANCE_Description,STAFF_ID,BRANCH_ID)
values(1, 'Food Distribution', 'Winter Relief', 150000, '2025-01-05', 'Initial funding for food packages', 1, 1),
(2, 'Clothing Drive', 'Charity Fair', 120000, '2025-01-06', 'Purchase of winter clothes', 2, 2),
(3, 'Health Support', 'Medical Camp', 200000, '2025-01-07', 'Medical supplies purchase', 3, 3),
(4, 'Shelter Homes', 'Fundraising Gala', 500000, '2025-01-08', 'Building shelters for families', 4, 4),
(5, 'School Kits', 'Back to School Event', 800000, '2025-01-09', 'Distribution of books and bags', 5, 5),
(6, 'Clean Water Project', 'Awareness Walk', 180000, '2025-01-10', 'Water purification system', 6, 6),
(7, 'Health Awareness', 'Community Seminar', 100000, '2025-01-11', 'Free health check-ups', 7, 7),
(8, 'Vocational Training', 'Youth Workshop', 950000, '2025-01-12', 'Tools and training material', 8, 8),
(9, 'Green Earth Project', 'Tree Planting Day', 600000, '2025-01-13', 'Saplings and garden tools', 9, 9),
(10, 'Winter Relief', 'Relief Drive', 2500000, '2025-01-14', 'Distribution of blankets and heaters', 10, 10),
(11, 'Orphan Support', 'Charity Marathon', 120000, '2025-01-15', 'Monthly orphan support fund', 11, 11),
(12, 'Disaster Relief', 'Emergency Fundraising', 750000, '2025-01-16', 'Flood relief activities', 12, 12),
(13, 'Microfinance Program', 'Business Fair', 1005000, '2025-01-17', 'Loans to small businesses', 13, 13),
(14, 'Education Outreach', 'Book Fair', 110000, '2025-01-18', 'Books for rural schools', 14, 14),
(15, 'Solar Energy Project', 'Tech Expo', 300000, '2025-01-19', 'Solar panel installation', 15, 15),
(16, 'Refugee Aid', 'Relief Camp', 4500000, '2025-01-20', 'Food and shelter for refugees', 16, 16),
(17, 'Mental Health Support', 'Awareness Program', 90500, '2025-01-21', 'Workshops and counseling', 17, 17),
(18, 'Livelihood Development', 'Skills Training', 1300000, '2025-01-22', 'Vocational course expenses', 18, 18),
(19, 'Vaccination Drive', 'Health Campaign', 2200000, '2025-01-23', 'Child vaccination costs', 19, 19),
(20, 'Infrastructure Repair', 'Community Project', 6000000, '2025-01-24', 'Repair of community roads', 20, 20);
select*from STATUS_T
insert into STATUS_T(STATUS_T,STATUS_Description,STATUS_lastUpdate,EVENTS_ID,PROJECT_ID)
values(
2, 'Clothing drive collection completed', '2025-01-06', 2, 2),
(3, 'Medical camp supplies delivered', '2025-01-07', 3, 3),
(4, 'Shelter construction 50% completed', '2025-01-08', 4, 4),
(5, 'School kits delivered to students', '2025-01-09', 5, 5),
(6, 'Water purification units installed', '2025-01-10', 6, 6),
(7, 'Health awareness campaign completed', '2025-01-11', 7, 7),
(8, 'Vocational training started for youth', '2025-01-12', 8, 8),
(9, 'Community trees planted successfully', '2025-01-13', 9, 9),
(10, 'Winter relief distribution ongoing', '2025-01-14', 10, 10),
(11, 'Orphan support fund disbursed', '2025-01-15', 11, 11),
(12, 'Emergency disaster relief in progress', '2025-01-16', 12, 12),
(13, 'Microfinance loans distributed', '2025-01-17', 13, 13),
(14, 'Education outreach workshops started', '2025-01-18', 14, 14),
(15, 'Solar panels installed successfully', '2025-01-19', 15, 15),
(16, 'Refugee shelter aid distributed', '2025-01-20', 16, 16),
(17, 'Mental health awareness sessions held', '2025-01-21', 17, 17),
(18, 'Livelihood training completed', '2025-01-22', 18, 18),
(19, 'Vaccination campaign completed', '2025-01-23', 19, 19),
(20, 'Infrastructure repairs finalized', '2025-01-24', 20, 20);
select*from REPORTS
 insert into REPORTS( REPORT_ID,REPORT_Title,REPORT_Date,REPORT_Type,PROJECT_ID,EVENTS_ID)
 values(1, 'Food Distribution Progress Report', '2025-01-05', 'Progress', 1, 1),
(2, 'Clothing Drive Summary', '2025-01-06', 'Final', 2, 2),
(3, 'Medical Camp Daily Report', '2025-01-07', 'Daily', 3, 3),
(4, 'Shelter Construction Status', '2025-01-08', 'Progress', 4, 4),
(5, 'School Kit Distribution Report', '2025-01-09', 'Final', 5, 5),
(6, 'Clean Water Project Monitoring', '2025-01-10', 'Progress', 6, 6),
(7, 'Health Awareness Seminar Report', '2025-01-11', 'Event Summary', 7, 7),
(8, 'Vocational Training Midterm Report', '2025-01-12', 'Progress', 8, 8),
(9, 'Tree Planting Day Report', '2025-01-13', 'Event Summary', 9, 9),
(10, 'Winter Relief Update', '2025-01-14', 'Progress', 10, 10),
(11, 'Orphan Support Monthly Report', '2025-01-15', 'Monthly', 11, 11),
(12, 'Disaster Relief Operation Report', '2025-01-16', 'Emergency', 12, 12),
(13, 'Microfinance Loan Disbursement Report', '2025-01-17', 'Finance', 13, 13),
(14, 'Education Outreach Progress Report', '2025-01-18', 'Progress', 14, 14),
(15, 'Solar Energy Installation Report', '2025-01-19', 'Technical', 15, 15),
(16, 'Refugee Aid Distribution Report', '2025-01-20', 'Progress', 16, 16),
(17, 'Mental Health Workshop Report', '2025-01-21', 'Event Summary', 17, 17),
(18, 'Livelihood Training Final Report', '2025-01-22', 'Final', 18, 18),
(19, 'Vaccination Campaign Report', '2025-01-23', 'Health', 19, 19),
(20, 'Infrastructure Repair Completion Report', '2025-01-24', 'Final', 20, 20);

 update DONATION set STAFF_ID=5 where DONATION_ID=6
 alter table donor add  DONOR_PhoneNum int;
 update DONOR set DONOR_PhoneNum=0771550591 where DONOR_ID=1
 update DONOR set DONOR_PhoneNum=0771550562 where DONOR_ID=2
 update DONOR set DONOR_PhoneNum=0771550563 where DONOR_ID=3
 update DONOR set DONOR_PhoneNum=0771550564 where DONOR_ID=4
 update DONOR set DONOR_PhoneNum=0771550565 where DONOR_ID=5
 update DONOR set DONOR_PhoneNum=0771550566 where DONOR_ID=6
 update DONOR set DONOR_PhoneNum=0771550567 where DONOR_ID=7
 update DONOR set DONOR_PhoneNum=0771550569 where DONOR_ID=8
 update DONOR set DONOR_PhoneNum=0771550568 where DONOR_ID=9
 update DONOR set DONOR_PhoneNum=0771550510 where DONOR_ID=10
 update DONOR set DONOR_PhoneNum=0771550511 where DONOR_ID=11
 update DONOR set DONOR_PhoneNum=0771550512 where DONOR_ID=12
 update DONOR set DONOR_PhoneNum=0771550513 where DONOR_ID=13
 update DONOR set DONOR_PhoneNum=0771550514 where DONOR_ID=14
 update DONOR set DONOR_PhoneNum=0771550515 where DONOR_ID=15
 update DONOR set DONOR_PhoneNum=0771550516 where DONOR_ID=16
 update DONOR set DONOR_PhoneNum=0771550517 where DONOR_ID=17
 update DONOR set DONOR_PhoneNum=0771550518 where DONOR_ID=18
 update DONOR set DONOR_PhoneNum=07715505619 where DONOR_ID=19
 update DONOR set DONOR_PhoneNum=07715505620where DONOR_ID=20
 ---------------------------------------------
 use CHARITY_MANAGEMENT_SYSTEM
 select*from BENEFICIARY
 select*from BALANCE
 select*from BRANCH
 select*from CHARITY
 select*from DONATION
 select*from DONOR
 select*from EVENTS_T
 select*from PROJECT
 select*from STAFF
 select*from REPORTS
 select*from BALACE_PROJECT
 select*from BALANCE_EVENT
 select*from STAFF_PROJECT
 select*from BENEFICIARY_DONATION
 select*from BALACE_PROJECT
 select*from BENEFICIARY_EVENTS
 select*from  CHARITY_DONOR
 select*from DONATION_BALANCE
 ------------------------------------------------
 update BRANCH set BRANCH_Name= 'MBQ' where BRANCH_ID =1
 update BRANCH set BRANCH_Name= 'Helping Hands'  where BRANCH_ID =2
  update BRANCH set BRANCH_Name= 'Afghan Aid' where BRANCH_ID =3
   update BRANCH set BRANCH_Name= 'Relief for All' where BRANCH_ID =4
    update BRANCH set BRANCH_Name='Hope Foundation' where BRANCH_ID =5
	 update BRANCH set BRANCH_Name= 'Life Aid' where BRANCH_ID =6
	  update BRANCH set BRANCH_Name=  'Health First' where BRANCH_ID =7
	   update BRANCH set BRANCH_Name= 'Future Builders' where BRANCH_ID =8
	   update BRANCH set BRANCH_Name= 'Green Earth' where BRANCH_ID =9
	   update BRANCH set BRANCH_Name= 'Safe Haven' where BRANCH_ID =10
	   update BRANCH set BRANCH_Name= 'Bright Future' where BRANCH_ID =11
	   update BRANCH set BRANCH_Name= 'Community Aid' where BRANCH_ID =12
	   update BRANCH set BRANCH_Name= 'Youth Empowerment' where BRANCH_ID =13
	   update BRANCH set BRANCH_Name=  'Education for All' where BRANCH_ID =14
	   update BRANCH set BRANCH_Name= 'Energy for Life' where BRANCH_ID =15
	 update BRANCH set BRANCH_Name= 'Refugee Support' where BRANCH_ID =16
	  update BRANCH set BRANCH_Name=  'Mind Care' where BRANCH_ID =17
	   update BRANCH set BRANCH_Name= 'Livelihood Aid'  where BRANCH_ID =18
	    update BRANCH set BRANCH_Name= 'Vaccinate Kids' where BRANCH_ID =19
		 update BRANCH set BRANCH_Name= 'Build Toghether' where BRANCH_ID =20
		  select*from DONOR where donor_id =2
	  insert into CHARITY_DONOR( CHARITY_ID, DONOR_ID)
	  values(1,2),(2,3),(3,4),(4,5),(5,6),(6,7),(7,8),(8,9),(9,10),(10,11),(11,12),(12,13),(13,14),(14,15),(15,16),(16,17),(17,18),(18,19),(19,20),(20,21);
	  
	insert into BENEFICIARY_DONATION(BENEFICIARY_iD,DONATION_ID)
	values(1,2),(2,3),(3,4),(4,5),(5,6),(6,7),(7,8),(8,9),(9,10),(10,11),(11,12),(12,13),(13,14),(14,15),(15,16),(16,17),(17,18),(18,19),(19,20);
	  
	 insert into BENEFICIARY_EVENTS(BENEFICIARY_iD,EVENTS_ID)
	 values(1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20);
	  
	  insert into BALACE_PROJECT(BALANCE_ID,PROJECT_ID)
	  values(1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20);
	  
	  insert into STAFF_PROJECT(STAFF_ID,PROJECT_ID)
	  values(1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),(11,11),(12,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20);
	 
	  insert into DONATION_BALANCE(BALANCE_ID,DONATION_ID)
	 	values(1,2),(2,3),(3,4),(4,5),(5,6),(6,7),(7,8),(8,9),(9,10),(10,11),(11,12),(12,13),(13,14),(14,15),(15,16),(16,17),(17,18),(18,19),(19,20);
	  select*from DONATION_BALANCE

	   select*from donor STAFF_PROJECT
	SELECT TOP 20 STAFF_ID FROM STAFF ORDER BY staff_ID;
	select top  20 project_id from PROJECT order by PROJECT_ID;

	select*from BENEFICIARY where BENEFICIARY_ID=3
	delete DONATION_BALANCE where balance_id=2
	select*from STAFF_PROJECT
	select*from DONATION_BALANCE
	update DONOR set DONOR_PhoneNum =0771550591 where DONOR_ID=1
update DONOR set donor_LastName ='ahmadi' where DONOR_ID =8 or DONOR_ID =5
update DONOR set donor_LastName ='moradi' where DONOR_ID =7 or DONOR_ID =9
update DONOR set donor_LastName ='sahibzada' where DONOR_ID =13 or DONOR_ID =20
update DONOR set donor_LastName ='safdari' where DONOR_ID =4 or DONOR_ID =14

update DONOR set donor_LastName ='mohammdi' where DONOR_ID =6 or DONOR_ID =17
update DONOR set donor_LastName ='arifi' where DONOR_ID =19or DONOR_ID =9
update DONOR set donor_LastName ='moradi' where DONOR_ID =18 or DONOR_ID =16
update DONOR set donor_LastName ='haidari' where DONOR_ID =15 or DONOR_ID =11
select*from DONOR
update DONOR  set donor_lastname='sar' where DONOR_ID=3