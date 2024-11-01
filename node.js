const nodemailer = require("nodemailer");


const_nodemailer=async()=>{
    const transporter = nodemailer.createTransport({
  host: 'mail.openjavascript.info',
  port: 465,
  secure: true, // true for port 465, false for other ports
  auth: {
    user: "maddison53@ethereal.email",
    pass: "jn7jnAPss4f63QBp6D",
  },
});

const messge={


    from: '"Maddison Foo Koch 👻" <maddison53@ethereal.email>', // sender address
    to: "bar@example.com, baz@example.com", // list of receivers
    subject: "Hello ✔", // Subject line
    text: "Hello world?", // plain text body
    html: "<b>Hello world?</b>",


};
  // send mail with defined transport object
   await transporter.sendMail(messge,(err,info)=>{
    if(err){
        console.log(err);
    }
    else{
        console.log("mail sent:"+info.response);
    }
  });

  

}

module.exports={
    nodemailer,
}