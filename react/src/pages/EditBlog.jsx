import React, { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import ReactQuill from 'react-quill'
import 'react-quill/dist/quill.snow.css'
import { Button } from "../components/ui/button"
import { Input } from "../components/ui/input"
import { Label } from "../components/ui/label"
import { Textarea } from "../components/ui/textarea"
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from "../components/ui/card"
import TextEditor from '../components/TextEditor'
import { set } from 'react-hook-form'

const EditBlog = () => {
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [content, setContent] = useState('')
  const navigate = useNavigate()
  const getBlog=async (id)=>{
    try {
      const response=await fetch('http://localhost:8000/api/blogs/'+id,{
        method:'GET',
        headers: {
          'Content-Type': 'application/json',
        },
      })
      const data=await response.json()
      return data
    } catch (error) {
      console.log(error);
      
    }
  }
  const {id}=useParams()
useEffect(() => {
  getBlog(id).then((response) => {
    const data=response
    console.log(data);
  
    if(data){
      setTitle(data.title)
      setDescription(data.description)
      setContent(data.content)
    }
  })
 
}, [])
  const handleSubmit = async (e) => {
    e.preventDefault()
    
    // Here you would typically make an API call to your server
    // For this example, we'll just log the data
    fetch('http://localhost:8000/api/blogs/14', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ title, description, content }),
    }).then((response) => {
      return response.json()
    }).then((data) => {
        console.log(data);
        
      
    })
    // Simulate an API call

    // Redirect to the blogs page after submission
  }

  return (
    <div className="max-w-4xl mx-auto">
      <Card>
        <CardHeader>
          <CardTitle>Create a New Blog Post</CardTitle>
        </CardHeader>
        <form onSubmit={handleSubmit}>
          <CardContent className="space-y-4 mb-12">
            <div className="space-y-2">
              <Label htmlFor="title">Title</Label>
              <Input
                id="title"
                value={title}
                onChange={(e) => setTitle(e.target.value)}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="description">Description</Label>
              <Textarea
                id="description"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="content">Content</Label>
             <TextEditor defaulteValue={content} onChange={setContent}/>
            </div>
          </CardContent>
          <CardFooter>
            <Button type="submit" className="w-full">Create Blog Post</Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  )
}

export default EditBlog

