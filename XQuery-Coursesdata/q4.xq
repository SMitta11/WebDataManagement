(:For each different instructor, return an element tagged instructor that contains the name of the 
instructor and the number of courses taught by the instructor.:)

for $x in distinct-values(doc("reed.xml")//course/instructor)
    return 
    <instructor>
            {'&#xa;', 'Name: ', $x, '&#xa;', 'Course count: ', count(doc("reed.xml")//course[instructor = $x]), '&#xa;'}
    </instructor>