use ivor_paine_hospital

-- query 1: list of consultants and the doctors in their team
select 
    senior.name as 'Consultant Name',
    junior.name as 'Team Member',
    junior.position as 'Member Rank'
from doctor senior
join doctor junior on senior.doctor_id = junior.consultant_id

-- query 2: a list of wards with respective sisters, care units and staff nurses in charge of care units
select 
    w.ward_name as 'Ward name',
    nd.name as 'Day sister',
    nn.name as 'Night sister',
    cu.care_unit_no as 'Care Unit',
    ns.name as 'Staff Incharge Nurse'
from ward w
left join nurse nd on w.day_sister_no = nd.staff_no
left join nurse nn on w.night_sister_no = nn.staff_no
join careunit cu on w.ward_name = cu.ward_name
left join nurse ns on cu.incharge_staff_no = ns.staff_no

-- query 3: a list of patients and their complaints, treatments and dates of treatment
select 
    p.patient_name as 'Patient Name',
    c.description as 'Complaint',
    t.description as 'Treatment Given',
    m.date_started as 'Treatment Started',
    m.date_ended as 'Treatment Ended'
from patient p
join medicalRecord m on m.patient_no = p.patient_no
join complaint c on m.complaint_id = c.complaint_id
join treatment t on m.treatment_id = t.treatment_id

-- query 4: a list of junior houseman and their patients and the staff nurse for the care-unit of that patient
select 
    d.name as 'Junior Houseman',
    p.patient_name as 'Patient Name',
    cu.care_unit_no as 'Care Unit',
    n.name as 'Staff Incharge Nurse'
from doctor d
join patient p on d.doctor_id = p.doctor_id
join careunit cu on p.care_unit_no = cu.care_unit_no
join nurse n on cu.incharge_staff_no = n.staff_no
where d.position = 'junior houseman(jh)'

-- query 5: a list of consultants with a unique speciality
select 
    distinct d.name as 'Consultant Name',
    c.spec_name as 'Unique Speciality' 
from doctor d
join consultant c on d.doctor_id = c.doctor_id

-- query 6: a list of complaints, treatments given for that complaint and experience history of the doctor giving that particular treatment
select 
    c.description as 'Patient Complaint',
    t.description as 'Treatment Prescribed',
    d.name as 'Prescribing Doctor',
    pe.position as 'Past Job Title',
    pe.establishment as 'Past Hospital',
    pe.from_date as 'Worked From',
    pe.to_date as 'Worked To'
from medicalRecord m
join complaint c on m.complaint_id = c.complaint_id
join treatment t on m.treatment_id = t.treatment_id
join doctor d on m.doctor_id = d.doctor_id
left join preexperience pe on d.doctor_id = pe.doctor_id

-- query 7: a list of patients with more than one complaint and their treatments
-- Inserting duplicate values
insert into medicalRecord (patient_no, doctor_id, complaint_id, treatment_id, date_started, date_ended) values 
(1, 1, 2, 2, '2026-04-02', null),
(3, 3, 3, 3, '2026-04-07', null)

select 
    p.patient_name as 'Patient Name',
    c.description as 'Complaint',
    t.description as 'Treatment'
from patient p
join medicalRecord m on p.patient_no = m.patient_no
join complaint c on m.complaint_id = c.complaint_id
join treatment t on m.treatment_id = t.treatment_id
where p.patient_no in (
                        select patient_no from medicalRecord
                        group by patient_no
                        having count(distinct complaint_id) > 1 )

-- query 8: a list of patients grouped by treatment within complaint
select 
    c.description as 'Complaint',
    t.description as 'Treatment',
    p.patient_name as 'Patient Name'
from medicalRecord m
join complaint c on m.complaint_id = c.complaint_id
join treatment t on m.treatment_id = t.treatment_id
join patient p on m.patient_no = p.patient_no
order by c.description, t.description, p.patient_name


-- query 9: performance history for a particular doctor
select 
    d.name as 'Doctor Name',
    ph.date_recorded as 'Review Date',
    ph.grade as 'Performance Grade'
from doctor d
join performancehistory ph on d.doctor_id = ph.doctor_id
where d.name = 'dr. sana javed'

-- query 10: full medical details for a particular patient
select 
    p.patient_name as 'Patient',
    p.date_of_birth as 'Dob',
    w.ward_name as 'Ward',
    b.bed_no as 'Bed',
    b.date_admitted as 'Admitted',
    d.name as 'Doctor Assigned',
    c.description as 'Complaint',
    t.description as 'Treatment',
    m.date_started as 'Treatment Started',
    m.date_ended as 'Treatment Ended'
from patient p
left join bed b on p.patient_no = b.patient_no
join ward w on b.ward_name = w.ward_name
join doctor d on p.doctor_id = d.doctor_id
join medicalRecord m on p.patient_no = m.patient_no
left join complaint c on m.complaint_id = c.complaint_id
left join treatment t on m.treatment_id = t.treatment_id
where p.patient_name = 'Asim Muneer'

-- query 11: list of treatments that have been given for a particular complaint between two given dates ordered by treatment
select 
    t.description as 'Treatment',
    c.description as 'Complaint',
    m.date_started as 'Date Given'
from medicalRecord m
join treatment t on m.treatment_id = t.treatment_id
join complaint c on m.complaint_id = c.complaint_id
where 
    c.description = 'severe chest pain'
    and m.date_started between '2026-04-01' and '2026-04-30'
order by t.description

-- query 12: list of the different positions held by staff in the hospital and a count of the number of staff in each position
select 
    hospital_staff.position as 'position',
    count(*) as 'total staff'
from (
    select position from doctor
    union all
    select 'day sister' from daysister
    union all
    select 'night sister' from nightsister
    union all
    select 'staff nurse' from staffnurse
    union all
    select 'non-registered nurse' from nonregsister) as hospital_staff
group by hospital_staff.position
order by count(*) desc, hospital_staff.position