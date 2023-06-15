(:For each different instructor, return an element tagged instructor that contains the name of the instructor and the titles of all courses taught by the instructor.:)

for $x in distinct-values(doc("reed.xml")//course/instructor)
    return 
    <instructor>
        {'&#xa;', 'Name: ', $x, '&#xa;', 'Courses: ', doc("reed.xml")//course[instructor = $x]/title, '&#xa;'}
    </instructor>