(:For each different course, return an element tagged course with the course title and all the instructor names that 
teach this course.:)

for $x in distinct-values(doc("reed.xml")//course/title)
    return
    
     <course>
     {'&#xa;','Course title:', $x, '&#xa;', 'Instructor:',doc("reed.xml")//course[title = $x]/instructor,'&#xa;'}

    </course>

