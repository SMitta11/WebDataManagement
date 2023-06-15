(:For each different department, display the department code and the number of courses taught by the department.:)

for $x in doc("reed.xml")//course
    let $d := $x/subj
    group by $d
    order by $d
    return 
        <dept>
            {'&#xa;','Department:', $d, '&#xa;', 'Number of courses:',count(distinct-values(doc("reed.xml")//course[subj = $d]/title)),'&#xa;'}
        </dept>
