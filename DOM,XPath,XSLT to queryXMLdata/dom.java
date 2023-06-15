import javax.xml.parsers.*;
import org.w3c.dom.*;
import java.net.URL;

class DOM {
    static void print ( Node e ) {
	if (e instanceof Text)
	    System.out.print(((Text) e).getData());
	else {
	    NodeList c = e.getChildNodes();
	    System.out.print("<"+e.getNodeName()+">");
	    for (int k = 0; k < c.getLength(); k++)
		print(c.item(k));
	    System.out.print("</"+e.getNodeName()+">");
	}
    }
    public static void main ( String args[] ) throws Exception {
	DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
	DocumentBuilder db = dbf.newDocumentBuilder();
	Document doc = db.parse((new URL("http://aiweb.cs.washington.edu/research/projects/xmltk/xmldata/data/courses/reed.xml")).openStream());
	Node root = doc.getDocumentElement();
	//print(root);
	 NodeList list = doc.getElementsByTagName("course");
	for (int temp = 0; temp < list.getLength(); temp++) {

              Node node = list.item(temp);

              if (node.getNodeType() == Node.ELEMENT_NODE) {

                  Element element = (Element) node;

                  // get text
                  String subjects = element.getElementsByTagName("subj").item(0).getTextContent();
                  String title = element.getElementsByTagName("title").item(0).getTextContent();
                  String place = element.getElementsByTagName("place").item(0).getTextContent();

				if(subjects.equals("MATH") && place.equals("LIB204")){
				//System.out.println("Current Element :" + node.getNodeName());
                	System.out.println("Subject title : " + title);
                  //System.out.println("place : " + place);
				}
              }
          }
    }
}
