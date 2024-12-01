import { useCallback, useEffect, useRef, useState } from 'react'
import RichTextEditor, {
  Attachment,
  BaseKit,
  Blockquote,
  Bold,
  BulletList,
  Clear,
  Code,
  CodeBlock,
  Color,
  ColumnActionButton,
  Emoji,
  Excalidraw,
  ExportPdf,
  ExportWord,
  FontFamily,
  FontSize,
  FormatPainter,
  Heading,
  Highlight,
  History,
  HorizontalRule,
  Iframe,
  Image,
  ImageGif,
  ImportWord,
  Indent,
  Italic,
  Katex,
  LineHeight,
  Link,
  Mention,
  Mermaid,
  MoreMark,
  OrderedList,
  SearchAndReplace,
  SlashCommand,
  Strike,
  Table,
  TableOfContents,
  TaskList,
  TextAlign,
  TextDirection,
  Twitter,
  Underline,
  Video,
  locale,
} from 'reactjs-tiptap-editor'

import 'reactjs-tiptap-editor/style.css'
import 'katex/dist/katex.min.css'
function convertBase64ToBlob(base64) {
  const arr = base64.split(',')
  const mime = arr[0].match(/:(.*?);/)[1]
  const bstr = atob(arr[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n)
  }
  return new Blob([u8arr], { type: mime })
} 
function extractImageUrls(content) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(content, 'text/html');
  const images = doc.querySelectorAll('img');
  return Array.from(images).map((img) => img.src);
}

function debounce(func, wait) {
  let timeout
  return function (...args) {
    clearTimeout(timeout)
    // @ts-ignore
    timeout = setTimeout(() => func.apply(this, args), wait)
  }
}
const extensions = [
  BaseKit.configure({
    placeholder: {
      showOnlyCurrent: true,
    },
    characterCount: {
      limit: 50_000,
    },
  }),
  History,
  SearchAndReplace,
  TableOfContents,
  FormatPainter.configure({ spacer: true }),
  Clear,
  FontFamily,
  Heading.configure({ spacer: true }),
  FontSize,
  Bold,
  Italic,
  Underline,
  Strike,
  MoreMark,
  Katex,
  Emoji,
  Color.configure({ spacer: true }),
  Highlight,
  BulletList,
  OrderedList,
  TextAlign.configure({ types: ['heading', 'paragraph'], spacer: true }),
  Indent,
  LineHeight,
  TaskList.configure({
    spacer: true,
    taskItem: {
      nested: true,
    },
  }),
  Link,
  Image.configure({
    upload: (files) => {
      return   new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('image', files);
      
        fetch('http://localhost:8000/api/images', {
          method: 'POST',
          body: formData,
        })
          .then((response) => response.json())
          .then((result) => {
            if (result.url) {
              resolve(result.url); // Resolve with just the URL string
            } else {
              reject(result.error || 'Upload failed');
            }
          })
          .catch((error) => {
            reject(error.message || 'Upload error');
          });
      });
      
     
      
      
    },
    
  }),
  Video.configure({
    upload: (files) => {
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve(URL.createObjectURL(files))
        }, 500)
      })
    },
  }),
  ImageGif.configure({
    GIPHY_API_KEY: import.meta.env.VITE_GIPHY_API_KEY ,
  }),
  Blockquote,
  SlashCommand,
  HorizontalRule,
  Code.configure({
    toolbar: false,
  }),
  CodeBlock.configure({ defaultTheme: 'dracula' }),
  ColumnActionButton,
  Table,
  Iframe,
  ExportPdf.configure({ spacer: true }),
  ImportWord.configure({

    upload: (files) => {
      const f = files.map(file => ({
        src: URL.createObjectURL(file),
        alt: file.name,
      }))
      return Promise.resolve(f)
    },
  }),
  ExportWord,
  Excalidraw,
  TextDirection,
  Mention,
  Attachment.configure({
    upload: (file) => {
      // fake upload return base 64
      const reader = new FileReader()
      reader.readAsDataURL(file)

      return new Promise((resolve) => {
        setTimeout(() => {
          const blob = convertBase64ToBlob(reader.result)
          resolve(URL.createObjectURL(blob))
        }, 300)
      })
    },
  }),
  Mermaid.configure({
    upload: (file) => {
      // fake upload return base 64
      const reader = new FileReader()
      reader.readAsDataURL(file)

      return new Promise((resolve) => {
        setTimeout(() => {
          const blob = convertBase64ToBlob(reader.result )
          resolve(URL.createObjectURL(blob))
        }, 300)
      })
    },
  }),
  Twitter,
]
function TextEditor({onChange,defaulteValue}) {
  const [content, setContent] = useState('')
  const [theme, setTheme] = useState('light')
  const [disable, setDisable] = useState(false)
const editorRef =useRef(null)

useEffect(() => {
    editorRef.current.editor.commands.setContent(defaulteValue)
      
},[defaulteValue])
  const onValueChange = useCallback(
    debounce((value) => {
      console.log('onValueChange',value);
      
      // Détecter et gérer les suppressions d'images
      const currentImages = extractImageUrls(value);
      const previousImages = extractImageUrls(content);

      // Identifier les images supprimées
      const deletedImages = previousImages.filter(
        (url) => !currentImages.includes(url)
      );

      // Supprimer chaque image côté serveur
      deletedImages.forEach((url) => {
        fetch('http://localhost:8000/api/images/delete', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ url }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error('Erreur lors de la suppression de l’image');
            }
            console.log(`Image supprimée: ${url}`);
          })
          .catch((error) => {
            console.error('Erreur lors de la suppression de l’image:', error);
          });
      });
      setContent(value)
      onChange(value)
    }, 300),

    [content]
  );

  return (
    <div
      className="flex flex-col w-full max-w-screen-lg gap-[24px]  my-0"
      style={{
        maxWidth: 1024,
      }}
    >
      <RichTextEditor
        ref={editorRef}
        output="html"
        content={content }
        onChangeContent={onValueChange}
        extensions={extensions}
        dark={theme === 'dark'}
        disabled={disable}
      />
{/* 
      {typeof content === 'string' && (
              <div style={{ marginTop: 20 ,height: 200,width: '100%'}} dangerouslySetInnerHTML={{ __html: content }} />
          )} */}
    </div>
  )
}

export default TextEditor
