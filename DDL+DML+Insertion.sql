

drop database ivor_paine_hospital
create database ivor_paine_hospital
use ivor_paine_hospital
-- ==============================================================================
-- table creation (ddl)
-- ==============================================================================

-- tables without any foreign keys
create table specialty (
    spec_name varchar(50) primary key )

create table complaint (
    complaint_id int primary key,
    description varchar(200) not null )

create table treatment (
    treatment_id int primary key,
    description varchar(200) not null )

-- consultant and doctor table and their constraints
create table doctor (
    doctor_id int primary key,
    name varchar(100) not null,
    position varchar(30) not null check(position in ('student(s)', 'junior houseman(jh)', 'senior houseman(sh)', 'assistant registrar(ar)', 'registrar(r)', 'consultant')),
    consultant_id int )

create table consultant (
    doctor_id int primary key,
    date_joined date not null,
    spec_name varchar(50) not null,
    foreign key (doctor_id) references doctor(doctor_id),
    foreign key (spec_name) references specialty(spec_name) )

alter table doctor add constraint fk_doc_consultant foreign key (consultant_id) references consultant(doctor_id)

-- weak entities
create table performancehistory (
    doctor_id int,
    date_recorded date,
    grade varchar(10) not null,
    primary key (doctor_id, date_recorded),
    foreign key (doctor_id) references doctor(doctor_id) )

create table preexperience (
    doctor_id int,
    from_date date,
    to_date date not null,
    position varchar(30) not null,
    establishment varchar(100) not null,
    primary key (doctor_id, from_date),
    foreign key (doctor_id) references doctor(doctor_id) )

-- ward and careunits alongwith nurses and their constraints
create table ward (
    ward_name varchar(50) primary key,
    spec_name varchar(50) not null,
    day_sister_no int,
    night_sister_no int,
    foreign key (spec_name) references specialty(spec_name) )

create table careunit (
    care_unit_no int primary key,
    ward_name varchar(50) not null,
    incharge_staff_no int,
    foreign key (ward_name) references ward(ward_name) )

-- nurse superclass and subclass
create table nurse (
    staff_no int primary key,
    name varchar(100) not null,
    ward_name varchar(50) not null,
    care_unit_no int not null,
    foreign key (ward_name) references ward(ward_name),
    foreign key (care_unit_no) references careunit(care_unit_no) )

create table daysister (
    staff_no int primary key,
    foreign key (staff_no) references nurse(staff_no) )

create table nightsister (
    staff_no int primary key,
    foreign key (staff_no) references nurse(staff_no) )

create table staffnurse (
    staff_no int primary key,
    foreign key (staff_no) references nurse(staff_no) )

create table nonregsister (
    staff_no int primary key,
    foreign key (staff_no) references nurse(staff_no) )

alter table ward add constraint fk_ward_day foreign key (day_sister_no) references daysister(staff_no)
alter table ward add constraint fk_ward_night foreign key (night_sister_no) references nightsister(staff_no)
alter table careunit add constraint fk_careunit_incharge foreign key (incharge_staff_no) references staffnurse(staff_no)

-- Patient, bed and medicalRecord
create table patient (
    patient_no int primary key,
    patient_name varchar(100) not null,
    date_of_birth date not null,
    care_unit_no int not null,
    doctor_id int not null,
    foreign key (care_unit_no) references careunit(care_unit_no),
    foreign key (doctor_id) references doctor(doctor_id) )

create table bed (
    bed_no int primary key,
    ward_name varchar(50) not null,
    patient_no int,
    date_admitted date not null,
    foreign key (ward_name) references ward(ward_name),
    foreign key (patient_no) references patient(patient_no) )

create table medicalRecord (
    patient_no int,
    doctor_id int,
    complaint_id int,
    treatment_id int,
    date_started date not null,
    date_ended date,
    primary key (patient_no, doctor_id, complaint_id, treatment_id, date_started),
    foreign key (patient_no) references patient(patient_no),
    foreign key (doctor_id) references doctor(doctor_id),
    foreign key (complaint_id) references complaint(complaint_id),
    foreign key (treatment_id) references treatment(treatment_id) )

-- ==============================================================================
-- data insertion (dml)
-- ==============================================================================

-- 10 specialties
insert into specialty (spec_name) values 
('cardiology'), ('orthopedics'), ('neurology'), ('pediatrics'), ('oncology'), 
('general surgery'), ('dermatology'), ('psychiatry'), ('gastroenterology'), ('pulmonology')

-- 10+ complaints
insert into complaint (complaint_id, description) values 
(1, 'severe chest pain'), (2, 'fractured femur'), (3, 'chronic migraines'), (4, 'high fever and chills'),
(5, 'unexplained weight loss'), (6, 'appendicitis'), (7, 'severe skin rash'), (8, 'acute anxiety'),
(9, 'stomach ulcer'), (10, 'asthma attack'), (11, 'broken arm'), (12, 'food poisoning')

-- 10+ treatments
insert into treatment (treatment_id, description) values 
(1, 'angioplasty'), (2, 'leg cast and crutches'), (3, 'beta blockers'), (4, 'intravenous paracetamol'),
(5, 'chemotherapy session 1'), (6, 'appendectomy'), (7, 'topical steroids'), (8, 'cognitive behavioral therapy'),
(9, 'omeprazole regimen'), (10, 'albuterol nebulizer'), (11, 'arm cast'), (12, 'iv fluids and rest')

-- 10 doctors (3 consultants and 7 juniors)
-- first we create 3 doctors with null consultants to make them consultants
-- then we create the junior doctor with senior doctors as their consultants
insert into doctor (doctor_id, name, position, consultant_id) values 
(1, 'dr. Shams Farooq', 'consultant', null),
(2, 'dr. Sheryar Rashid', 'consultant', null),
(3, 'dr. Faisal Cheema', 'consultant', null)

insert into doctor (doctor_id, name, position, consultant_id) values 
(4, 'dr. ahsan nawaz', 'registrar(r)', 1),
(5, 'dr. bilal ahmed', 'assistant registrar(ar)', 1),
(6, 'dr. usman ghani', 'senior houseman(sh)', 2),
(7, 'dr. talha rabbani', 'junior houseman(jh)', 2),
(8, 'dr. ali sheikh', 'student(s)', 2),
(9, 'dr. zahid raza', 'senior houseman(sh)', 3),
(10, 'dr. ahmed ghauri', 'junior houseman(jh)', 3)

-- 3 consultants
insert into consultant (doctor_id, date_joined, spec_name) values 
(1, '2015-06-01', 'cardiology'),
(2, '2018-03-15', 'pediatrics'),
(3, '2012-11-20', 'general surgery')

-- doctor history and pre experience
insert into performancehistory (doctor_id, date_recorded, grade) values 
(4, '2025-06-01', 'a'), (4, '2025-12-01', 'a+'), (5, '2025-06-01', 'b'), (5, '2025-12-01', 'a'),
(6, '2025-06-01', 'b+'), (7, '2025-06-01', 'a'), (8, '2025-12-01', 'c'), (9, '2025-06-01', 'a'),
(10, '2025-12-01', 'b+'), (4, '2024-12-01', 'a')

insert into preexperience (doctor_id, from_date, to_date, position, establishment) values 
(1, '2010-01-01', '2015-05-31', 'registrar(r)', 'aga khan university hospital, karachi'),
(2, '2014-02-01', '2018-02-28', 'assistant registrar(ar)', 'shifa international, islamabad'),
(3, '2008-05-01', '2012-10-31', 'registrar(r)', 'SK hospital, lahore'),
(4, '2023-01-01', '2024-01-01', 'senior houseman(sh)', 'pims, islamabad'),
(5, '2023-06-01', '2024-06-01', 'junior houseman(jh)', 'jinnah hospital, karachi'),
(6, '2024-01-01', '2024-12-31', 'junior houseman(jh)', 'cmh, rawalpindi'),
(9, '2023-01-01', '2024-01-01', 'junior houseman(jh)', 'civil hospital, karachi'),
(10, '2024-05-01', '2025-05-01', 'student(s)', 'dow university hospital'),
(7, '2024-08-01', '2025-01-01', 'student(s)', 'holy family hospital, rawalpindi'),
(8, '2025-01-01', '2025-06-01', 'student(s)', 'pims, islamabad')

-- 10 wards (fks null for now)
insert into ward (ward_name, spec_name, day_sister_no, night_sister_no) values 
('edhi ward', 'pediatrics', null, null), ('jinnah ward', 'cardiology', null, null), 
('iqbal ward', 'general surgery', null, null), ('liaquat ward', 'orthopedics', null, null), 
('fatima ward', 'neurology', null, null), ('sir syed ward', 'oncology', null, null), 
('chughtai ward', 'dermatology', null, null), ('shaukat ward', 'psychiatry', null, null), 
('abdus salam ward', 'gastroenterology', null, null), ('faiz ward', 'pulmonology', null, null)

-- 10 care units (incharge fk null for now)
insert into careunit (care_unit_no, ward_name, incharge_staff_no) values 
(101, 'edhi ward', null), (102, 'edhi ward', null), (201, 'jinnah ward', null), 
(202, 'jinnah ward', null), (301, 'iqbal ward', null), (302, 'iqbal ward', null), 
(401, 'liaquat ward', null), (501, 'fatima ward', null), (601, 'sir syed ward', null), 
(701, 'chughtai ward', null)

-- 15 nurses
insert into nurse (staff_no, name, ward_name, care_unit_no) values 
(1, 'fatima tariq', 'edhi ward', 101), (2, 'zainab ali', 'jinnah ward', 201), 
(3, 'maryam nawaz', 'iqbal ward', 301), (4, 'aisha sadiq', 'liaquat ward', 401), 
(5, 'khadija omer', 'fatima ward', 501), (6, 'hira mani', 'edhi ward', 102), 
(7, 'iqra aziz', 'jinnah ward', 202), (8, 'nida yasir', 'iqbal ward', 302), 
(9, 'rabia butt', 'edhi ward', 101), (10, 'sana makbul', 'jinnah ward', 201), 
(11, 'sadia imam', 'iqbal ward', 301), (12, 'kiran haq', 'edhi ward', 102), 
(13, 'farah shah', 'jinnah ward', 202), (14, 'madiha rizvi', 'iqbal ward', 302), 
(15, 'nadia hussain', 'liaquat ward', 401)

-- categorize the 15 nurses
insert into daysister (staff_no) values (1), (2)
insert into nightsister (staff_no) values (3), (4)
insert into staffnurse (staff_no) values (5), (6), (7), (8), (9), (10)
insert into nonregsister (staff_no) values (11), (12), (13), (14), (15)

-- update wards and care units with nurse fks
update ward set day_sister_no = 1, night_sister_no = 3 where ward_name = 'edhi ward'
update ward set day_sister_no = 2, night_sister_no = 4 where ward_name = 'jinnah ward'
update careunit set incharge_staff_no = 5 where care_unit_no = 101
update careunit set incharge_staff_no = 6 where care_unit_no = 102
update careunit set incharge_staff_no = 7 where care_unit_no = 201
update careunit set incharge_staff_no = 8 where care_unit_no = 202
update careunit set incharge_staff_no = 9 where care_unit_no = 301
update careunit set incharge_staff_no = 10 where care_unit_no = 302

-- 30 patients
-- 
insert into patient (patient_no, patient_name, date_of_birth, care_unit_no, doctor_id) values 
(1, 'Lionel Messi', '1985-04-12', 201, 1), (2, 'Cristiano Ronaldo', '1990-08-25', 202, 4),
(3, 'Mohamed Salah', '2015-11-03', 101, 2), (4, 'Asim Muneer', '2018-01-15', 102, 7),
(5, 'Qamar Bajwa', '1975-09-30', 301, 3), (6, 'Babar Azam', '1981-12-05', 302, 9),
(7, 'Haris Rauf', '1984-05-18', 401, 5), (8, 'Shaheen Afridi', '1994-03-22', 501, 6),
(9, 'Neymar Jr', '1992-07-11', 201, 1), (10, 'Kylian Mbappe', '1988-02-14', 301, 10),
(11, 'Erling Haaland', '1982-10-09', 202, 4), (12, 'Kevin De Bruyne', '1989-06-27', 101, 8),
(13, 'Mohammad Rizwan', '1983-04-16', 302, 3), (14, 'Shadab Khan', '1991-01-05', 401, 5),
(15, 'Naseem Shah', '1965-08-20', 201, 1), (16, 'Fakhar Zaman', '1984-04-05', 501, 6),
(17, 'Virat Kohli', '1981-09-02', 102, 2), (18, 'MS Dhoni', '1992-05-26', 301, 9),
(19, 'Rohit Sharma', '1984-07-02', 401, 5), (20, 'Karim Benzema', '1989-07-30', 202, 4),
(21, 'Luka Modric', '1988-12-01', 302, 10), (22, 'Hassaan Mehmood', '2005-08-05', 101, 7),
(23, 'Wasim Akram', '1988-08-09', 201, 1), (24, 'Waqar Younis', '1991-07-02', 501, 6),
(25, 'Shoaib Akhtar', '1992-09-28', 102, 8), (26, 'Shahid Afridi', '1984-09-14', 301, 3),
(27, 'Imran Khan', '1965-11-05', 401, 5), (28, 'Pele', '1969-10-23', 202, 4),
(29, 'Diego Maradona', '1986-07-14', 302, 9), (30, 'Ronaldinho', '1998-11-20', 101, 2);

-- 30 beds
insert into bed (bed_no, ward_name, patient_no, date_admitted) values 
(1, 'jinnah ward', 1, '2026-04-01'), (2, 'jinnah ward', 9, '2026-04-09'), 
(3, 'jinnah ward', 15, '2026-04-15'), (4, 'jinnah ward', 23, '2026-04-23'),
(5, 'jinnah ward', 2, '2026-04-02'), (6, 'jinnah ward', 11, '2026-04-11'), 
(7, 'jinnah ward', 20, '2026-04-20'), (8, 'jinnah ward', 28, '2026-04-28'),
(9, 'edhi ward', 3, '2026-04-03'), (10, 'edhi ward', 12, '2026-04-12'), 
(11, 'edhi ward', 22, '2026-04-22'), (12, 'edhi ward', 30, '2026-04-30'),
(13, 'edhi ward', 4, '2026-04-04'), (14, 'edhi ward', 17, '2026-04-17'), 
(15, 'edhi ward', 25, '2026-04-25'), (16, 'iqbal ward', 5, '2026-04-05'),
(17, 'iqbal ward', 10, '2026-04-10'), (18, 'iqbal ward', 18, '2026-04-18'), 
(19, 'iqbal ward', 26, '2026-04-26'), (20, 'iqbal ward', 6, '2026-04-06'),
(21, 'iqbal ward', 13, '2026-04-13'), (22, 'iqbal ward', 21, '2026-04-21'), 
(23, 'iqbal ward', 29, '2026-04-29'), (24, 'liaquat ward', 7, '2026-04-07'),
(25, 'liaquat ward', 14, '2026-04-14'), (26, 'liaquat ward', 19, '2026-04-19'), 
(27, 'liaquat ward', 27, '2026-04-27'), (28, 'fatima ward', 8, '2026-04-08'),
(29, 'fatima ward', 16, '2026-04-16'), (30, 'fatima ward', 24, '2026-04-24')

-- 30 checkup history records (receives)
insert into medicalRecord (patient_no, doctor_id, complaint_id, treatment_id, date_started, date_ended) values 
(1, 1, 1, 1, '2026-04-01', null), (2, 4, 1, 3, '2026-04-02', '2026-04-10'),
(3, 2, 4, 4, '2026-04-03', '2026-04-06'), (4, 7, 10, 10, '2026-04-04', '2026-04-12'),
(5, 3, 6, 6, '2026-04-05', '2026-04-15'), (6, 9, 9, 9, '2026-04-06', null),
(7, 5, 2, 2, '2026-04-07', null), (8, 6, 3, 4, '2026-04-08', '2026-04-14'),
(9, 1, 1, 1, '2026-04-09', null), (10, 10, 6, 6, '2026-04-10', '2026-04-20'),
(11, 4, 1, 3, '2026-04-11', '2026-04-25'), (12, 8, 4, 4, '2026-04-12', '2026-04-16'),
(13, 3, 6, 6, '2026-04-13', null), (14, 5, 11, 11, '2026-04-14', null),
(15, 1, 1, 1, '2026-04-15', null), (16, 6, 3, 4, '2026-04-16', '2026-04-20'),
(17, 2, 4, 4, '2026-04-17', '2026-04-22'), (18, 9, 9, 9, '2026-04-18', null),
(19, 5, 2, 2, '2026-04-19', null), (20, 4, 1, 3, '2026-04-20', null),
(21, 10, 6, 6, '2026-04-21', null), (22, 7, 11, 11, '2026-04-22', null),
(23, 1, 1, 1, '2026-04-23', null), (24, 6, 3, 4, '2026-04-24', null),
(25, 8, 4, 4, '2026-04-25', null), (26, 3, 6, 6, '2026-04-26', null),
(27, 5, 11, 11, '2026-04-27', null), (28, 4, 1, 3, '2026-04-28', null),
(29, 9, 9, 9, '2026-04-29', null), (30, 2, 4, 4, '2026-04-30', null)





select count(*) as total_patients from patient
select count(*) as total_doctors from doctor
select count(*) as total_nurses from nurse
select count(*) as total_beds from bed
select count(*) as total_records from medicalRecord

select * from patient
select doctor_id, name, position, consultant_id from doctor


select ward_name, spec_name, day_sister_no, night_sister_no from ward

select 
    p.patient_name as 'Name',
    d.name as 'Doctor'
from patient p
join doctor d on p.doctor_id = d.doctor_id


select junior.name as "Junior Doctor", 
       junior.position as Rank,
       senior.name as 'Team Consultant'
from doctor junior join doctor senior 
     on junior.consultant_id = senior.doctor_id
order by senior.name


select 
        p.patient_name as 'Patient',
        c.description as 'Complaint',
        t.description as 'Treatment Given',
        d.name as 'Attending Doctor',
        m.date_started as "Started"
from medicalRecord m  
join patient p on m.patient_no = p.patient_no
join complaint c on m.complaint_id = c.complaint_id
join treatment t on m.treatment_id = t.treatment_id
join doctor d on m.doctor_id = d.doctor_id


