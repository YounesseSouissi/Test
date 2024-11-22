import { useState, useRef, useEffect } from "react";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";

function App() {
  const [content, setContent] = useState("");
  const quillRef = useRef(null); // Référence pour l'éditeur Quill
  const imageHandler = () => {
    const input = document.createElement("input");
    input.setAttribute("type", "file");
    input.setAttribute("accept", "image/*");
    input.click();

    input.onchange = async () => {
      const file = input.files[0];
      if (file) {
        const formData = new FormData();
        formData.append("image", file);

        try {
          // Appeler votre API pour télécharger l'image
          const response = await fetch(
            "http://localhost:8000/api/images",
            {
              method: "POST",
              body: formData,
            }
          );

          const data = await response.json();
          const imageUrl = data.url;

          const quill = quillRef.current.getEditor();
          const range = quill.getSelection();
          
          quill.insertEmbed(range.index, "image", imageUrl);
        } catch (error) {
          console.error("Erreur lors du téléchargement de l'image :", error);
        }
      }
    };
  }

  const modules = {
    toolbar: {
      container: [
        [{ header: [1, 2, false] }],
        ["bold", "italic", "underline"],
        ["image", "link"],
      ],
      handlers: {
        image:imageHandler
      },
    },
  };

  const handleChange = (value) => {
    setContent(value);
  };

  const saveBlog = async () => {
    try {
      await fetch("http://localhost:8000/api/blogs", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ content }),
      });
      alert("Blog enregistré avec succès !");
    } catch (error) {
      console.error("Erreur lors de l'enregistrement du blog :", error);
    }
  };

  return (
    <>
      <ReactQuill
        ref={quillRef} // Associer la référence
        value={content}
        modules={modules}
        onChange={handleChange}
      />
      <button onClick={saveBlog}>Save Blog</button>

    </>
  );
}

export default App;
