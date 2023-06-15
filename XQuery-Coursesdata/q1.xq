
(:Q1 For each MATH course taught in room LIB 204, return an element tagged course with the title, the instructor, 
the start, and the end times of the course.:)

for $x in doc("reed.xml")/root/course
        where ($x/subj='MATH' and $x/place[building='LIB' and room=204])
        return
                <course>
                {'&#xa;','Course title: ', $x/title/text(), '&#xa;', 
                'Instructor: ',$x/instructor/text(),'&#xa;',
                'Start time: ',$x/time/start_time/text(),'&#xa;',
                'End time: ',$x/time/end_time/text(),'&#xa;'
                }
        </course>


       

