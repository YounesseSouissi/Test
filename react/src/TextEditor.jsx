import React, { useState } from 'react';
import { CKEditor } from '@ckeditor/ckeditor5-react';
import { 
  ClassicEditor, 
  Bold, 
  Essentials, 
  Italic, 
  Underline, 
  Link, 
  List, 
  Paragraph, 
  Undo, 
  Font, 
  Image, 
  ImageToolbar, 
  ImageCaption, 
  ImageStyle, 
  ImageUpload 
} from 'ckeditor5';

import 'ckeditor5/ckeditor5.css';

const TextEditor = () => {
  const [editorData, setEditorData] = useState('<p>Écrivez votre texte ici...</p>');

  const config = {
    toolbar: {
      items: [
        'undo', '|',
        'bold', 'italic', 'underline', '|',
        'fontSize', 'fontFamily', 'fontColor', '|',
        'bulletedList', 'numberedList', '|',
        'link', 'imageUpload', '|',
        
      ],
    },
    plugins: [
      Essentials,
      Bold,
      Italic,
      Underline,
      Link,
      List,
      Paragraph,
      Undo,
      Font,
      Image,
      ImageToolbar,
      ImageCaption,
      ImageStyle,
      ImageUpload
    ],
    fontFamily: {
      options: [
        'default',
        'Arial, Helvetica, sans-serif',
        'Courier New, Courier, monospace',
        'Georgia, serif',
        'Times New Roman, Times, serif',
        'Verdana, Geneva, sans-serif'
      ],
    },
    fontSize: {
      options: ['tiny', 'small', 'default', 'big', 'huge'],
    },
    fontColor: {
      columns: 5,
      documentColors: 10,
    },
    image: {
      toolbar: ['imageTextAlternative', '|', 'imageStyle:full', 'imageStyle:side'],
    },
  };

  // Custom upload adapter to handle image uploa

  // Add the custom upload adapter to the editor
  const uploadAdapterPlugin = (editor) => {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
      return new MyUploadAdapter(loader);
    };
  };

  return (
    <div style={{ margin: '20px' }}>
      <h2>Éditeur de Texte</h2>
      <CKEditor
        editor={ClassicEditor}
        config={{
          ...config,
        }}
        data={editorData}
        onChange={(event, editor) => {
          const data = editor.getData();
          setEditorData(data);
        }}
      />

      <div style={{ marginTop: '20px' }}>
        <h3>Contenu de l'éditeur :</h3>
        <div dangerouslySetInnerHTML={{ __html: editorData }} />
      </div>
    </div>
  );
};

export default TextEditor;
  class MyUploadAdapter {
    constructor(loader) {
      this.loader = loader;
    }

    // Simulates the upload process
    upload() {
      return this.loader.file
        .then((file) => new Promise((resolve, reject) => {
          const formData = new FormData();
          formData.append('image', file);

          fetch('https://your-server.com/upload', {
            method: 'POST',
            body: formData,
          })
            .then((response) => response.json())
            .then((result) => {
              if (result.url) {
                resolve({
                  default: result.url, // URL to be used in the image src
                });
              } else {
                reject(result.error || 'Upload failed');
              }
            })
            .catch((error) => {
              reject(error.message || 'Upload error');
            });
        }));
    }

    // Optional: Called when the upload is aborted
    abort() {
      console.log('Upload aborted');
    }
  }