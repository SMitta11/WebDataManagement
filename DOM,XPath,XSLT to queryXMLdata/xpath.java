import javax.xml.xpath.*;
import org.xml.sax.InputSource;
import org.w3c.dom.*;

class XPATH {

    static void print ( Node e ) {
		if (e instanceof Text)
			System.out.println(((Text) e).getData());
		else {
			NodeList c = e.getChildNodes();
			System.out.println("<"+e.getNodeName());
			NamedNodeMap attributes = e.getAttributes();
			for (int i = 0; i < attributes.getLength(); i++)
			System.out.println(" "+attributes.item(i).getNodeName()
				+"=\""+attributes.item(i).getNodeValue()+"\"");
			System.out.println(">");
			for (int k = 0; k < c.getLength(); k++)
			print(c.item(k));
			System.out.println("</"+e.getNodeName()+">");
		}
    }

    static void eval ( String query, String document ) throws Exception {
		XPathFactory xpathFactory = XPathFactory.newInstance();
		XPath xpath = xpathFactory.newXPath();
		InputSource inputSource = new InputSource(document);
		NodeList result = (NodeList) xpath.evaluate(query,inputSource,XPathConstants.NODESET);
		System.out.println("XPath query: "+query);
		for (int i = 0; i < result.getLength(); i++)
			print(result.item(i));
		System.out.println();
    }

    public static void main ( String[] args ) throws Exception {
	
	//titles of all MATH courses that are taught in room LIB 204
	eval("/root//course[subj='MATH' and place/building='LIB' and place/room=204]/title/text()","reed.xml");
	System.out.println();

	//instructor name who teaches MATH 412
	eval("/root//course[subj='MATH' and crse='412']/instructor/text()","reed.xml");
	System.out.println();

	//titles of all courses taught by Wieting
	eval("/root//course[instructor='Wieting']/title/text()","reed.xml");
	System.out.println();
    }
}
